<?php
declare(strict_types=1);

const TARGET_PHP_VERSION = '8.3.19';
const MINIMUM_PHP_VERSION = '8.3.0';

function btRequirePhpRuntime(): void
{
    if (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, '<')) {
        throw new RuntimeException('PHP ' . MINIMUM_PHP_VERSION . '+ is required. Configure the hosting account for PHP ' . TARGET_PHP_VERSION . '.');
    }
}

function btLoadDatabaseConfig(): array
{
    $path = __DIR__ . DIRECTORY_SEPARATOR . 'db-config.php';
    if (!is_file($path)) {
        throw new RuntimeException('Missing db-config.php. Upload the database configuration file beside index.php and api.php.');
    }

    $config = require $path;
    if (!is_array($config)) {
        throw new RuntimeException('db-config.php must return a database configuration array.');
    }

    foreach (['host', 'port', 'database', 'username', 'password', 'charset'] as $key) {
        if (!array_key_exists($key, $config) || trim((string) $config[$key]) === '') {
            throw new RuntimeException('db-config.php is missing the required "' . $key . '" value.');
        }
    }

    return $config;
}

function btPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    btRequirePhpRuntime();

    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('The pdo_mysql PHP extension is required for MySQL storage.');
    }

    $config = btLoadDatabaseConfig();
    $charset = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $config['charset']) ?: 'utf8mb4';
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        (string) $config['host'],
        (int) $config['port'],
        (string) $config['database'],
        $charset
    );

    $pdo = new PDO($dsn, (string) $config['username'], (string) $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function btCreateSchema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_users (
            username VARCHAR(40) NOT NULL,
            display_name VARCHAR(120) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) NOT NULL DEFAULT 0,
            must_reset_password TINYINT(1) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at VARCHAR(40) NOT NULL,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_sessions (
            token CHAR(64) NOT NULL,
            username VARCHAR(40) NOT NULL,
            expires_at INT UNSIGNED NOT NULL,
            created_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (token),
            KEY idx_bt_sessions_username (username),
            KEY idx_bt_sessions_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_app_state (
            storage_key VARCHAR(191) NOT NULL,
            storage_value LONGTEXT NOT NULL,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (storage_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_tournaments (
            tournament_id VARCHAR(191) NOT NULL,
            name VARCHAR(255) NOT NULL,
            scheduled_date VARCHAR(32) NOT NULL DEFAULT '',
            data_json LONGTEXT NOT NULL,
            data_hash CHAR(64) NOT NULL,
            version INT UNSIGNED NOT NULL DEFAULT 1,
            created_at VARCHAR(40) NOT NULL,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (tournament_id),
            KEY idx_bt_tournaments_scheduled_date (scheduled_date),
            KEY idx_bt_tournaments_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_tournament_matches (
            tournament_id VARCHAR(191) NOT NULL,
            match_id VARCHAR(191) NOT NULL,
            stage VARCHAR(80) NOT NULL DEFAULT '',
            group_index SMALLINT NULL,
            knockout_round SMALLINT NULL,
            team1 VARCHAR(255) NOT NULL DEFAULT '',
            team2 VARCHAR(255) NOT NULL DEFAULT '',
            score1 SMALLINT UNSIGNED NULL,
            score2 SMALLINT UNSIGNED NULL,
            data_json LONGTEXT NOT NULL,
            version INT UNSIGNED NOT NULL DEFAULT 1,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (tournament_id, match_id),
            KEY idx_bt_tournament_matches_tournament (tournament_id),
            KEY idx_bt_tournament_matches_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_player_lists (
            owner_username VARCHAR(40) NOT NULL DEFAULT '',
            list_id VARCHAR(191) NOT NULL,
            name VARCHAR(255) NOT NULL,
            player_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            data_json LONGTEXT NOT NULL,
            data_hash CHAR(64) NOT NULL,
            version INT UNSIGNED NOT NULL DEFAULT 1,
            created_at VARCHAR(40) NOT NULL,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (owner_username, list_id),
            KEY idx_bt_player_lists_name (owner_username, name),
            KEY idx_bt_player_lists_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bt_app_settings (
            owner_username VARCHAR(40) NOT NULL DEFAULT '',
            storage_key VARCHAR(191) NOT NULL,
            storage_value LONGTEXT NOT NULL,
            updated_at VARCHAR(40) NOT NULL,
            PRIMARY KEY (owner_username, storage_key),
            KEY idx_bt_app_settings_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    btEnsureOwnedPrimaryKey($pdo, 'bt_player_lists', 'list_id');
    btEnsureOwnedPrimaryKey($pdo, 'bt_app_settings', 'storage_key');
}

function btTableExists(PDO $pdo, string $table): bool
{
    $statement = $pdo->prepare('
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = ?
    ');
    $statement->execute([$table]);
    return (bool) $statement->fetchColumn();
}

function btColumnExists(PDO $pdo, string $table, string $column): bool
{
    $statement = $pdo->prepare('
        SELECT COUNT(*)
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = ?
          AND column_name = ?
    ');
    $statement->execute([$table, $column]);
    return (bool) $statement->fetchColumn();
}

function btIndexExists(PDO $pdo, string $table, string $indexName): bool
{
    $statement = $pdo->prepare('
        SELECT COUNT(*)
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = ?
          AND index_name = ?
    ');
    $statement->execute([$table, $indexName]);
    return (bool) $statement->fetchColumn();
}

function btPrimaryKeyColumns(PDO $pdo, string $table): array
{
    $statement = $pdo->prepare('
        SELECT column_name
        FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE()
          AND table_name = ?
          AND constraint_name = "PRIMARY"
        ORDER BY ordinal_position
    ');
    $statement->execute([$table]);
    return array_map('strval', $statement->fetchAll(PDO::FETCH_COLUMN));
}

function btEnsureOwnedPrimaryKey(PDO $pdo, string $table, string $idColumn): void
{
    $safeTable = str_replace('`', '', $table);
    $safeIdColumn = str_replace('`', '', $idColumn);

    if (!btColumnExists($pdo, $table, 'owner_username')) {
        $pdo->exec("ALTER TABLE `$safeTable` ADD COLUMN owner_username VARCHAR(40) NOT NULL DEFAULT '' FIRST");
    }

    if (btPrimaryKeyColumns($pdo, $table) !== ['owner_username', $idColumn]) {
        $pdo->exec("ALTER TABLE `$safeTable` DROP PRIMARY KEY, ADD PRIMARY KEY (owner_username, `$safeIdColumn`)");
    }

    $ownerIndex = $table === 'bt_player_lists' ? 'idx_bt_player_lists_owner_updated' : 'idx_bt_app_settings_owner_updated';
    if (!btIndexExists($pdo, $table, $ownerIndex)) {
        $pdo->exec("ALTER TABLE `$safeTable` ADD KEY `$ownerIndex` (owner_username, updated_at)");
    }
}

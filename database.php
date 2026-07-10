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
}

function btTableExists(PDO $pdo, string $table): bool
{
    $statement = $pdo->prepare('SHOW TABLES LIKE ?');
    $statement->execute([$table]);
    return (bool) $statement->fetchColumn();
}

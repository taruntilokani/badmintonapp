<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'database.php';

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/plain; charset=utf-8');

echo "Badminton Tournament - InfinityFree MySQL Diagnostic\n";
echo "====================================================\n";
echo "PHP executed: YES\n";
echo "Target PHP version: " . TARGET_PHP_VERSION . "\n";
echo "Current PHP version: " . PHP_VERSION . "\n";
echo "PHP 8.3 compatible: " . (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, '>=') ? 'YES' : 'NO') . "\n";
echo "pdo_mysql loaded: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n";
echo "index.php exists: " . (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'index.php') ? 'YES' : 'NO') . "\n";
echo "api.php exists: " . (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'api.php') ? 'YES' : 'NO') . "\n";
echo "database.php exists: " . (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'database.php') ? 'YES' : 'NO') . "\n";
echo "db-config.php exists: " . (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'db-config.php') ? 'YES' : 'NO') . "\n";
echo "open_basedir: " . (ini_get('open_basedir') ?: 'not set') . "\n";
echo "Server API: " . PHP_SAPI . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n\n";

try {
    btRequirePhpRuntime();
    $config = btLoadDatabaseConfig();
    echo "Configured database: " . (string) $config['database'] . "\n";
    echo "Configured MySQL host: " . (string) $config['host'] . "\n";
    echo "Configured MySQL port: " . (string) $config['port'] . "\n";
    echo "Configured MySQL user: " . (string) $config['username'] . "\n";
    $pdo = btPdo();
    echo "Database connection: PASS\n";
    btCreateSchema($pdo);
    echo "Schema creation/check: PASS\n";
    foreach (['bt_users', 'bt_sessions', 'bt_app_state', 'bt_tournaments', 'bt_tournament_matches', 'bt_player_lists', 'bt_app_settings'] as $table) {
        echo $table . " table exists: " . (btTableExists($pdo, $table) ? 'YES' : 'NO') . "\n";
    }
    echo "User rows: " . (int) $pdo->query('SELECT COUNT(*) FROM bt_users')->fetchColumn() . "\n\n";
    echo "Tournament rows: " . (int) $pdo->query('SELECT COUNT(*) FROM bt_tournaments')->fetchColumn() . "\n";
    echo "Match score rows: " . (int) $pdo->query('SELECT COUNT(*) FROM bt_tournament_matches')->fetchColumn() . "\n";
    echo "Player list rows: " . (int) $pdo->query('SELECT COUNT(*) FROM bt_player_lists')->fetchColumn() . "\n";
    echo "Settings rows: " . (int) $pdo->query('SELECT COUNT(*) FROM bt_app_settings')->fetchColumn() . "\n\n";
    echo "RESULT: MySQL is ready. Open index.php and log in with admin/admin, then change the password.\n";
} catch (Throwable $error) {
    echo "Database connection/schema: FAIL\n";
    echo "Error: " . $error->getMessage() . "\n\n";
    echo "RESULT: Check PHP 8.3.x, pdo_mysql, db-config.php, and the InfinityFree MySQL database credentials.\n";
}

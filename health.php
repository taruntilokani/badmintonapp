<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'database.php';

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/html; charset=utf-8');

$configExists = is_file(__DIR__ . DIRECTORY_SEPARATOR . 'db-config.php');
$indexExists = is_file(__DIR__ . DIRECTORY_SEPARATOR . 'index.php');
$apiExists = is_file(__DIR__ . DIRECTORY_SEPARATOR . 'api.php');
$databaseHelperExists = is_file(__DIR__ . DIRECTORY_SEPARATOR . 'database.php');
$phpSupported = version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, '>=');
$pdoMysqlLoaded = extension_loaded('pdo_mysql');
$databaseConnected = false;
$schemaReady = false;
$usersCount = null;
$dbName = '';
$dbHost = '';
$dbUser = '';
$errorMessage = '';

try {
    btRequirePhpRuntime();
    $config = btLoadDatabaseConfig();
    $dbName = (string) $config['database'];
    $dbHost = (string) $config['host'];
    $dbUser = (string) $config['username'];
    $pdo = btPdo();
    $databaseConnected = true;
    btCreateSchema($pdo);
    $schemaReady = btTableExists($pdo, 'bt_users')
        && btTableExists($pdo, 'bt_sessions')
        && btTableExists($pdo, 'bt_app_state');
    if ($schemaReady) {
        $usersCount = (int) $pdo->query('SELECT COUNT(*) FROM bt_users')->fetchColumn();
    }
} catch (Throwable $error) {
    $errorMessage = $error->getMessage();
}

$allGood = $phpSupported
    && $pdoMysqlLoaded
    && $configExists
    && $indexExists
    && $apiExists
    && $databaseHelperExists
    && $databaseConnected
    && $schemaReady;
$status = $allGood ? 'READY' : 'ACTION REQUIRED';
$statusColor = $allGood ? '#177245' : '#b42318';

function yesNo(bool $value): string
{
    return $value ? 'Yes' : 'No';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Badminton App Health</title>
  <style>
    body { max-width: 820px; margin: 40px auto; padding: 0 18px; font: 16px/1.5 system-ui, sans-serif; color: #182230; }
    h1 { margin-bottom: 8px; }
    .status { color: <?= e($statusColor) ?>; font-weight: 800; }
    table { width: 100%; border-collapse: collapse; margin: 22px 0; }
    th, td { padding: 10px; border: 1px solid #d0d5dd; text-align: left; }
    th { width: 48%; background: #f8fafc; }
    code { background: #f2f4f7; padding: 2px 5px; border-radius: 4px; }
    .warning { background: #fff4e5; border: 1px solid #fdb022; padding: 12px; border-radius: 8px; }
  </style>
</head>
<body>
  <h1>Badminton App Health</h1>
  <div class="status"><?= e($status) ?></div>
  <table>
    <tr><th>PHP executing</th><td>Yes</td></tr>
    <tr><th>Target PHP version</th><td><?= e(TARGET_PHP_VERSION) ?></td></tr>
    <tr><th>Current PHP version</th><td><?= e(PHP_VERSION) ?></td></tr>
    <tr><th>PHP 8.3 or newer</th><td><?= yesNo($phpSupported) ?></td></tr>
    <tr><th><code>pdo_mysql</code> extension loaded</th><td><?= yesNo($pdoMysqlLoaded) ?></td></tr>
    <tr><th><code>index.php</code> exists</th><td><?= yesNo($indexExists) ?></td></tr>
    <tr><th><code>api.php</code> exists</th><td><?= yesNo($apiExists) ?></td></tr>
    <tr><th><code>database.php</code> exists</th><td><?= yesNo($databaseHelperExists) ?></td></tr>
    <tr><th><code>db-config.php</code> exists</th><td><?= yesNo($configExists) ?></td></tr>
    <tr><th>Configured database</th><td><?= e($dbName ?: '-') ?></td></tr>
    <tr><th>Configured MySQL host</th><td><?= e($dbHost ?: '-') ?></td></tr>
    <tr><th>Configured MySQL user</th><td><?= e($dbUser ?: '-') ?></td></tr>
    <tr><th>Database connected</th><td><?= yesNo($databaseConnected) ?></td></tr>
    <tr><th>Database tables ready</th><td><?= yesNo($schemaReady) ?></td></tr>
    <tr><th>User rows</th><td><?= $usersCount === null ? '-' : e((string) $usersCount) ?></td></tr>
  </table>
  <?php if (!$allGood): ?>
    <div class="warning">
      Configure PHP <?= e(TARGET_PHP_VERSION) ?> or PHP 8.3.x, upload <code>database.php</code> and <code>db-config.php</code>, and confirm the InfinityFree MySQL credentials are correct.
      <?php if ($errorMessage !== ''): ?>
        <br><br><b>Last error:</b> <?= e($errorMessage) ?>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <p>The server is ready. Open <a href="index.php">the tournament app</a> and sign in with <code>admin</code> / <code>admin</code>. The first API request creates the default admin account if the users table is empty.</p>
  <?php endif; ?>
</body>
</html>

<?php
declare(strict_types=1);

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/html; charset=utf-8');

$storage = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
$phpSupported = version_compare(PHP_VERSION, '7.0.0', '>=');
$directoryExists = is_dir($storage);
$directoryWritable = $directoryExists && is_writable($storage);
$usersStore = $storage . DIRECTORY_SEPARATOR . 'users.php';
$allGood = $phpSupported && $directoryExists && $directoryWritable;
$status = $allGood ? 'READY' : 'ACTION REQUIRED';
$statusColor = $allGood ? '#177245' : '#b42318';

function yesNo(bool $value): string
{
    return $value ? 'Yes' : 'No';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tournament Server Health</title>
  <style>
    body { max-width: 760px; margin: 40px auto; padding: 0 18px; font: 16px/1.5 system-ui, sans-serif; color: #182230; }
    h1 { margin-bottom: 8px; }
    .status { color: <?= htmlspecialchars($statusColor, ENT_QUOTES, 'UTF-8') ?>; font-weight: 800; }
    table { width: 100%; border-collapse: collapse; margin: 22px 0; }
    th, td { padding: 10px; border: 1px solid #d0d5dd; text-align: left; }
    th { width: 45%; background: #f8fafc; }
    code { background: #f2f4f7; padding: 2px 5px; border-radius: 4px; }
    .warning { background: #fff4e5; border: 1px solid #fdb022; padding: 12px; border-radius: 8px; }
  </style>
</head>
<body>
  <h1>Tournament Server Health</h1>
  <div class="status"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></div>
  <table>
    <tr><th>PHP executing</th><td>Yes</td></tr>
    <tr><th>PHP version</th><td><?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></td></tr>
    <tr><th>PHP 7.0 or newer</th><td><?= yesNo($phpSupported) ?></td></tr>
    <tr><th>Storage directory exists</th><td><?= yesNo($directoryExists) ?></td></tr>
    <tr><th>Storage directory writable</th><td><?= yesNo($directoryWritable) ?></td></tr>
    <tr><th>User store initialized</th><td><?= yesNo(is_file($usersStore)) ?></td></tr>
  </table>
  <?php if (!$allGood): ?>
    <div class="warning">
      Enable a PHP 7.0+ profile for this Web Station portal and grant the Web Station <code>http</code> account read/write access to the <code>storage</code> directory.
    </div>
  <?php else: ?>
    <p>The server is ready. Return to <a href="index.html">the tournament app</a> and sign in with <code>admin</code> / <code>admin</code>.</p>
  <?php endif; ?>
</body>
</html>

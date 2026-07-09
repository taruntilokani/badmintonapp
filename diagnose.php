<?php
/* Minimal PHP 7.0-compatible Web Station diagnostic. */
header('Cache-Control: no-store');
header('Content-Type: text/plain; charset=utf-8');

$storage = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
$testFile = $storage . DIRECTORY_SEPARATOR . '.write-test-' . uniqid('', true);
$directoryExists = is_dir($storage);
$reportedWritable = $directoryExists && is_writable($storage);
$bytesWritten = false;
$testDeleted = false;

if ($directoryExists) {
    $bytesWritten = @file_put_contents($testFile, 'Web Station write test', LOCK_EX);
    if ($bytesWritten !== false) {
        $testDeleted = @unlink($testFile);
    }
}

echo "Badminton Tournament - Synology Diagnostic\n";
echo "==========================================\n";
echo "PHP executed: YES\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "PHP 7.0 compatible: " . (version_compare(PHP_VERSION, '7.0.0', '>=') ? 'YES' : 'NO') . "\n";
echo "Storage directory exists: " . ($directoryExists ? 'YES' : 'NO') . "\n";
echo "Storage reports writable: " . ($reportedWritable ? 'YES' : 'NO') . "\n";
echo "Actual file write test: " . ($bytesWritten !== false ? 'PASS' : 'FAIL') . "\n";
echo "Test file cleanup: " . ($bytesWritten === false ? 'NOT NEEDED' : ($testDeleted ? 'PASS' : 'FAIL')) . "\n";
echo "open_basedir: " . (ini_get('open_basedir') ?: 'not set') . "\n";
echo "Server API: " . PHP_SAPI . "\n\n";

if (!$directoryExists) {
    echo "RESULT: Create the storage directory beside api.php.\n";
} elseif ($bytesWritten === false) {
    echo "RESULT: Grant the Synology Web Station http user/group read and write permission on the storage directory.\n";
} else {
    echo "RESULT: PHP and storage are ready. Upload the latest api.php and index.html, then retry admin/admin.\n";
}

<?php

declare(strict_types=1);

$functionsTests = glob(__DIR__ . '/functions_*.php') ?: [];
$showTests = glob(__DIR__ . '/show_*.php') ?: [];
$actionTests = glob(__DIR__ . '/action_*.php') ?: [];
$graphicTests = glob(__DIR__ . '/graphic_*.php') ?: [];
$tests = array_merge($functionsTests, $showTests, $actionTests, $graphicTests);
sort($tests);

$failed = false;

foreach ($tests as $test) {
    echo "Running $test\n";
    passthru('php ' . escapeshellarg($test), $exitCode);
    if ($exitCode !== 0) {
        $failed = true;
    }
}

if ($failed) {
    exit(1);
}

echo "All include/tests checks passed.\n";

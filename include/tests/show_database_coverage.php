<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$showFiles = glob(dirname(__DIR__) . '/show/*.php') ?: [];
$showStems = [];
foreach ($showFiles as $showFile) {
    $showStems[] = pathinfo($showFile, PATHINFO_FILENAME);
}
sort($showStems);

$testFiles = glob(__DIR__ . '/show_*_database.php') ?: [];
$testStems = [];
foreach ($testFiles as $testFile) {
    $baseName = basename($testFile, '.php');
    $prefixLength = strlen('show_');
    $suffixLength = strlen('_database');
    $testStems[] = substr($baseName, $prefixLength, strlen($baseName) - $prefixLength - $suffixLength);
}
sort($testStems);

assertSameValue(
    $showStems,
    $testStems,
    'Every include/show/{filename}.php has exactly one include/tests/show_{filename}_database.php test file (and no orphan show database tests).'
);

foreach ($showStems as $stem) {
    $testPath = __DIR__ . '/show_' . $stem . '_database.php';
    assertTrue(is_file($testPath), 'Expected test exists: ' . basename($testPath));

    $lintOutput = [];
    $lintExitCode = 0;
    exec('php -l ' . escapeshellarg($testPath), $lintOutput, $lintExitCode);
    assertSameValue(0, $lintExitCode, basename($testPath) . ' passes php -l syntax check.');
}

finishTest('show_database_coverage.php');

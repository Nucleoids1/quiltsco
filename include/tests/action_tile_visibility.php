<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$path = dirname(__DIR__) . '/action/tile_visibility.php';
assertTrue(is_file($path), 'tile_visibility.php action exists.');

$lintOutput = [];
$lintExitCode = 0;
exec('php -l ' . escapeshellarg($path), $lintOutput, $lintExitCode);
assertSameValue(0, $lintExitCode, 'tile_visibility.php passes php -l syntax check.');

$contents = file_get_contents($path);
assertTrue($contents !== false, 'tile_visibility.php is readable.');
if ($contents !== false) {
    assertContains("postInt('visibility', null, true)", $contents, 'tile_visibility.php reads visibility from POST and allows negative values.');
    assertContains("getInt('visibility', null, true)", $contents, 'tile_visibility.php falls back to GET visibility and allows negative values.');
    assertContains('in_array($visibility, array(-1, 0, 1), true)', $contents, 'tile_visibility.php constrains visibility to expected values.');
}

finishTest('action_tile_visibility.php');

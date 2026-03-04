<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$path = dirname(__DIR__) . '/action/new_complete.php';
assertTrue(is_file($path), 'new_complete.php action exists.');

$lintOutput = [];
$lintExitCode = 0;
exec('php -l ' . escapeshellarg($path), $lintOutput, $lintExitCode);
assertSameValue(0, $lintExitCode, 'new_complete.php passes php -l syntax check.');

$contents = file_get_contents($path);
assertTrue($contents !== false, 'new_complete.php is readable.');
if ($contents !== false) {
    assertContains("require_once('../include/functions/password_policy.php');", $contents, 'new_complete.php loads shared password policy helper.');
    assertContains('validatePasswordForAuthFlow($_pass1, $_pass2)', $contents, 'new_complete.php validates passwords through shared helper.');
    assertContains('makeCookie(\'notice\', $passwordError);', $contents, 'new_complete.php surfaces helper validation messages to users.');
}

finishTest('action_new_complete.php');

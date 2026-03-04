<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$path = dirname(__DIR__) . '/action/test_email.php';
assertTrue(is_file($path), 'test_email.php action exists.');

$lintOutput = [];
$lintExitCode = 0;
exec('php -l ' . escapeshellarg($path), $lintOutput, $lintExitCode);
assertSameValue(0, $lintExitCode, 'test_email.php passes php -l syntax check.');

$contents = file_get_contents($path);
assertTrue($contents !== false, 'test_email.php is readable.');
if ($contents !== false) {
    assertContains("require_once('../include/functions/send_email.php');", $contents, 'test_email.php loads send_email helper.');
    assertContains('(int) $GLOBALS[\'auth\'][\'id\'] !== 1', $contents, 'test_email.php restricts test sending to user id 1.');
    assertContains('sendEmail($GLOBALS[\'auth\'][\'email\']', $contents, 'test_email.php sends the test email to the logged in account email.');
}

finishTest('action_test_email.php');

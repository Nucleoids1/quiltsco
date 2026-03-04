<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$path = dirname(__DIR__) . '/action/tracker_bugs_admin.php';
assertTrue(is_file($path), 'tracker_bugs_admin.php action exists.');
$contents = file_get_contents($path);
assertTrue($contents !== false, 'tracker_bugs_admin.php action is readable.');
if ($contents !== false) {
    assertContains("post('security_code')", $contents, 'tracker_bugs_admin.php reads security code from form submission.');
    assertContains('!$GLOBALS[\'auth\'][\'id\']', $contents, 'tracker_bugs_admin.php requires security code only for anonymous users.');
    assertContains('isSecurityCodeValid', $contents, 'tracker_bugs_admin.php validates security code with the shared helper for anonymous users.');
}

finishTest('action_tracker_bugs_admin_security_code.php');

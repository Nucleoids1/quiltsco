<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

$actionNewPath = dirname(__DIR__) . '/action/new.php';
assertTrue(is_file($actionNewPath), 'new.php action exists.');
$contents = file_get_contents($actionNewPath);
assertTrue($contents !== false, 'new.php action is readable.');
if ($contents !== false) {
    assertContains("post('security_code')", $contents, 'new.php reads security_code from form submission.');
    assertContains("isSecurityCodeValid", $contents, 'new.php validates security code using shared helper.');
}

$actionPasswordPath = dirname(__DIR__) . '/action/password.php';
assertTrue(is_file($actionPasswordPath), 'password.php action exists.');
$contents = file_get_contents($actionPasswordPath);
assertTrue($contents !== false, 'password.php action is readable.');
if ($contents !== false) {
    assertContains("post('security_code')", $contents, 'password.php reads security_code from form submission.');
    assertContains("isSecurityCodeValid", $contents, 'password.php validates security code using shared helper.');
}

finishTest('action_new_and_password_security_code.php');

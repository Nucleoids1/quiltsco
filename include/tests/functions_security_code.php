<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('security_code.php', ['isSecurityCodeValid']);

$path = dirname(__DIR__) . '/functions/security_code.php';
$contents = file_get_contents($path);
assertTrue($contents !== false, 'security_code.php is readable for content checks.');
if ($contents !== false) {
    assertContains('SecurityCodeLast', $contents, 'security_code.php checks logged-in users via SecurityCodeLast.');
    assertContains('SecurityCodeCache', $contents, 'security_code.php checks guests via SecurityCodeCache.');
}

finishTest('functions_security_code.php');

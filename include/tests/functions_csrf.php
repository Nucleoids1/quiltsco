<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/csrf.php';
require_once __DIR__ . '/../functions/general.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('csrf.php', ['csrfGetSecret', 'csrfGetUserIdentity', 'csrfGenerateToken', 'csrfGetToken', 'csrfValidateToken', 'csrfField', 'csrfCheck', 'csrfRegenerateToken']);

$_POST = [];
$_SERVER = [
    'REMOTE_ADDR' => '10.1.2.3',
    'HTTP_USER_AGENT' => 'UnitTestAgent/2.0',
    'REQUEST_METHOD' => 'POST',
    'REQUEST_URI' => '/submit',
];
$GLOBALS['auth'] = ['id' => 0];

$token = csrfGenerateToken();
assertTrue((bool) preg_match('/^\d+\.[a-f0-9]{64}$/', $token), 'Generated token format matches timestamp.hmac.');
assertTrue(csrfValidateToken($token), 'Freshly-generated token validates.');
assertContains('guest:10.1.2.3:', csrfGetUserIdentity(), 'Guest identity includes IP-based prefix.');

$_POST['csrf_token'] = $token;
assertTrue(csrfCheck(true), 'csrfCheck(debug=true) accepts valid token.');
assertFalse(csrfValidateToken('invalid-token'), 'Malformed token is rejected.');
assertContains('name="csrf_token"', csrfField(), 'csrfField renders csrf_token input field.');

$cachedA = csrfGetToken();
$cachedB = csrfGetToken();
assertSameValue($cachedA, $cachedB, 'csrfGetToken returns cached token in-process.');
assertTrue(csrfValidateToken(csrfRegenerateToken()), 'csrfRegenerateToken returns structurally valid token.');

finishTest('functions_csrf.php');

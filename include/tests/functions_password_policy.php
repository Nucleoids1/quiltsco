<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/login.php';
require_once __DIR__ . '/_functions_test_helpers.php';

if (!defined('PASSWORD_MIN')) {
    define('PASSWORD_MIN', 6);
}

if (!defined('PASSWORD_MAX')) {
    define('PASSWORD_MAX', 40);
}

require_once __DIR__ . '/../functions/password_policy.php';

assertFunctionFileContract('password_policy.php', ['passwordSecurityRequirementsText', 'validatePasswordForAuthFlow']);

assertSameValue('You need to enter a password.', validatePasswordForAuthFlow('', 'Whatever1!'), 'Missing first password returns enter-password notice.');
assertSameValue('You need to re-enter your password.', validatePasswordForAuthFlow('Whatever1!', ''), 'Missing confirmation returns re-enter notice.');
assertSameValue('Your passwords do not match.', validatePasswordForAuthFlow('Whatever1!', 'Different1!'), 'Mismatched passwords return mismatch notice.');
assertSameValue('Your password is not long enough. Your password must be at least ' . PASSWORD_MIN . ' characters long.', validatePasswordForAuthFlow('A1!a', 'A1!a'), 'Too-short password returns min-length notice.');
assertSameValue('Your password is too long. Your password cannot be more than ' . PASSWORD_MAX . ' characters long.', validatePasswordForAuthFlow(str_repeat('A', PASSWORD_MAX + 1) . '1!a', str_repeat('A', PASSWORD_MAX + 1) . '1!a'), 'Too-long password returns max-length notice.');
assertSameValue(passwordSecurityRequirementsText(), validatePasswordForAuthFlow('alllowercase1!', 'alllowercase1!'), 'Complexity failure returns shared security requirements message.');
assertSameValue(null, validatePasswordForAuthFlow('GoodPass1!', 'GoodPass1!'), 'Policy-compliant password returns no error.');

finishTest('functions_password_policy.php');

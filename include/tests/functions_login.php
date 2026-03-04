<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/login.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('login.php', ['ipMatchesSubnet', 'userAgentMatches', 'verifyPassword', 'needsPasswordMigration', 'needsPasswordRehash', 'createSessionToken', 'createUniqueIdentification', 'closeLogin', 'updateMemberOnlineSession', 'createMemberOnlineSession', 'removeMemberOnlineSession', 'processPostLogin', 'loadAuthMember', 'isPasswordSecure']);

// ip subnet matching
assertSameValue(true, ipMatchesSubnet(0, 1234), 'Zero encoded IP bypasses strict matching.');
assertSameValue(true, ipMatchesSubnet(3232235777, 3232235778), 'Default subnet bits allow nearby private IPs.');
assertSameValue(false, ipMatchesSubnet(3232235777, 167772161), 'Different subnet values fail matching.');

// user agent normalization
assertSameValue(true, userAgentMatches('', 'any'), 'Empty user-agent side is permissive.');
assertSameValue(true, userAgentMatches('Mozilla/5.0 (X11) Firefox/123.0', 'mozilla/5.0 firefox/122.1'), 'Version and parenthetical differences normalize to match.');
assertSameValue(false, userAgentMatches('Mozilla Firefox', 'Chrome'), 'Different user agents fail.');

// password helpers
$bcrypt = password_hash('S3cure!Pass', PASSWORD_DEFAULT);
assertSameValue(true, verifyPassword('S3cure!Pass', $bcrypt), 'verifyPassword validates password_hash hashes.');
assertSameValue(false, verifyPassword('wrong', $bcrypt), 'verifyPassword rejects wrong password for modern hashes.');
assertSameValue(true, verifyPassword('hello', md5('hello')), 'verifyPassword supports legacy md5 lowercase scheme.');
assertSameValue(true, needsPasswordMigration(md5('legacy')), 'Legacy md5 hash requires migration.');
assertSameValue(false, needsPasswordMigration($bcrypt), 'Modern hash does not require migration.');
assertSameValue(false, needsPasswordRehash(md5('legacy')), 'Legacy hashes are excluded from password_needs_rehash path.');
assertSameValue((bool) password_needs_rehash($bcrypt, PASSWORD_DEFAULT), needsPasswordRehash($bcrypt), 'needsPasswordRehash delegates to password_needs_rehash for modern hashes.');

// token helpers
$token1 = createSessionToken();
$token2 = createSessionToken();
assertTrue((bool) preg_match('/^[a-f0-9]{40}$/', $token1), 'createSessionToken returns 40-char hex token.');
assertSameValue(false, $token1 === $token2, 'createSessionToken generates non-identical random tokens.');

// password strength policy
assertSameValue(false, isPasswordSecure('Short1!'), 'Password shorter than 8 chars fails security policy.');
assertSameValue(false, isPasswordSecure('lowercase1!'), 'Missing uppercase fails security policy.');
assertSameValue(false, isPasswordSecure('UPPERCASE1!'), 'Missing lowercase fails security policy.');
assertSameValue(false, isPasswordSecure('NoDigits!!'), 'Missing digit fails security policy.');
assertSameValue(false, isPasswordSecure('NoSpecial1'), 'Missing special char fails security policy.');
assertSameValue(true, isPasswordSecure('GoodPass1!'), 'Mixed-case alphanumeric + symbol passes password policy.');

finishTest('functions_login.php');

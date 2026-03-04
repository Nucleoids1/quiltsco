<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/general.php';
require_once __DIR__ . '/../functions/send_email.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('send_email.php', ['smtpIsEnabled', 'smtpSafeHeaderValue', 'smtpHeloHostname', 'smtpEncryptionMode', 'smtpSendRawCommand', 'smtpSendMail', 'sendMailViaTransport', 'sendEmail']);

if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.example.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USERNAME')) define('SMTP_USERNAME', '');
if (!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD', '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', 'noreply@example.com');
if (!defined('SMTP_ENCRYPTION')) define('SMTP_ENCRYPTION', '');

$_SERVER['HTTP_HOST'] = 'www.Example.COM:8080';

assertSameValue(true, smtpIsEnabled(), 'smtpIsEnabled returns true when SMTP_HOST is defined and non-empty.');
assertSameValue('abc', smtpSafeHeaderValue("\r\nabc\n"), 'smtpSafeHeaderValue strips CRLF characters.');
assertSameValue('example.com', smtpHeloHostname(), 'smtpHeloHostname normalizes host and strips port/www prefix.');
assertSameValue('ssl', smtpEncryptionMode(465), 'Port 465 defaults to ssl when encryption is unconfigured.');
assertSameValue('tls', smtpEncryptionMode(587), 'Non-465 port defaults to tls when encryption is unconfigured.');

$socket = fopen('php://temp', 'r+');
fwrite($socket, "250 OK\r\n");
rewind($socket);
assertSameValue(true, smtpSendRawCommand($socket, '', [250]), 'smtpSendRawCommand accepts matching SMTP response code.');
fclose($socket);

$socket = fopen('php://temp', 'r+');
fwrite($socket, "550 Nope\r\n");
rewind($socket);
assertSameValue(false, smtpSendRawCommand($socket, '', [250]), 'smtpSendRawCommand rejects non-matching SMTP response code.');
fclose($socket);

finishTest('functions_send_email.php');

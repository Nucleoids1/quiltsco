<?php
function smtpIsEnabled()
{
    return defined('SMTP_HOST') && SMTP_HOST !== '';
}

function smtpSafeHeaderValue($value)
{
    return trim(str_replace(array("\r", "\n"), '', (string) $value));
}

function smtpHeloHostname()
{
    $host = smtpSafeHeaderValue(str_replace('www.', '', strtolower(server('HTTP_HOST'))));
    $host = preg_replace('/:.*/', '', $host);
    $host = preg_replace('/[^a-z0-9.-]/', '', $host);

    if ($host === '') {
        return 'localhost';
    }

    return $host;
}

function smtpEncryptionMode($port)
{
    $configured = strtolower(trim((string) SMTP_ENCRYPTION));

    if ($configured === 'starttls') {
        $configured = 'tls';
    }

    if ($configured === 'tls' || $configured === 'ssl' || $configured === 'none') {
        return $configured;
    }

    if ((int) $port === 465) {
        return 'ssl';
    }

    return 'tls';
}

function smtpSendRawCommand($socket, $command, $expectedCodes)
{
    if ($command !== '') {
        fwrite($socket, $command . "\r\n");
    }

    $response = '';
    while (!feof($socket)) {
        $line = fgets($socket, 515);
        if ($line === false) {
            break;
        }

        $response .= $line;
        if (strlen($line) < 4 || $line[3] !== '-') {
            break;
        }
    }

    $code = (int) substr(trim($response), 0, 3);
    if (!in_array($code, $expectedCodes, true)) {
        return false;
    }

    return true;
}

function smtpSendMail($to, $subject, $body, $headers)
{
    $host = SMTP_HOST;
    $port = (int) SMTP_PORT;
    $username = SMTP_USERNAME;
    $password = SMTP_PASSWORD;
    $encryption = smtpEncryptionMode($port);
    $from = smtpSafeHeaderValue(SMTP_FROM_EMAIL);
    $to = smtpSafeHeaderValue($to);
    $subject = smtpSafeHeaderValue($subject);

    $remoteHost = $host;
    if ($encryption === 'ssl') {
        $remoteHost = 'ssl://' . $host;
    }

    $socket = @fsockopen($remoteHost, $port, $errno, $errstr, 15);
    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 15);

    if (!smtpSendRawCommand($socket, '', array(220))) {
        fclose($socket);
        return false;
    }

    $ehloHost = smtpHeloHostname();

    if (!smtpSendRawCommand($socket, 'EHLO ' . $ehloHost, array(250))) {
        if (!smtpSendRawCommand($socket, 'HELO ' . $ehloHost, array(250))) {
            fclose($socket);
            return false;
        }
    }

    if ($encryption === 'tls') {
        if (!smtpSendRawCommand($socket, 'STARTTLS', array(220))) {
            fclose($socket);
            return false;
        }

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }

        if (!smtpSendRawCommand($socket, 'EHLO ' . $ehloHost, array(250))) {
            fclose($socket);
            return false;
        }
    }

    if ($username !== '') {
        if (
            !smtpSendRawCommand($socket, 'AUTH LOGIN', array(334)) ||
            !smtpSendRawCommand($socket, base64_encode($username), array(334)) ||
            !smtpSendRawCommand($socket, base64_encode($password), array(235))
        ) {
            fclose($socket);
            return false;
        }
    }

    if (
        !smtpSendRawCommand($socket, 'MAIL FROM:<' . $from . '>', array(250)) ||
        !smtpSendRawCommand($socket, 'RCPT TO:<' . $to . '>', array(250, 251)) ||
        !smtpSendRawCommand($socket, 'DATA', array(354))
    ) {
        fclose($socket);
        return false;
    }

    $mime = implode("\r\n", $headers) . "\r\n"
        . 'Subject: ' . $subject . "\r\n"
        . 'To: ' . $to . "\r\n\r\n"
        . $body;
    $mime = preg_replace('/(^|\r\n)\./', '$1..', $mime);

    fwrite($socket, $mime . "\r\n.\r\n");
    if (!smtpSendRawCommand($socket, '', array(250))) {
        fclose($socket);
        return false;
    }

    smtpSendRawCommand($socket, 'QUIT', array(221, 250));
    fclose($socket);

    return true;
}

function sendMailViaTransport($email, $subject, $body, $type = 'plain', $from = '', $replyTo = '')
{
    $fromEmail = smtpSafeHeaderValue($from ? $from : SMTP_FROM_EMAIL);
    $email = smtpSafeHeaderValue($email);
    $subject = smtpSafeHeaderValue($subject);
    $replyTo = smtpSafeHeaderValue($replyTo);

    $headers = array(
        'From: ' . $fromEmail,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/' . $type . '; charset=utf-8',
        'Content-Transfer-Encoding: 8bit'
    );

    if ($replyTo !== '') {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    if (smtpIsEnabled()) {
        if (smtpSendMail($email, $subject, $body, $headers)) {
            return;
        }
    }

    ini_set('sendmail_from', $fromEmail);
    @mail($email, $subject, $body, implode("\r\n", $headers) . "\r\n\r\n");
}

function sendEmail($email, $subject, $body, $type = 'plain', $from = '', $replyTo = '')
{
    sendMailViaTransport($email, $subject, $body, $type, $from, $replyTo);
}

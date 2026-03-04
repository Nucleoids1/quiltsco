<?php
function csrfGetSecret()
{
    static $secret = null;
    if ($secret === null) {
        $parts = [
            defined('SQL_HOST') ? SQL_HOST : '',
            defined('SQL_DATABASE') ? SQL_DATABASE : '',
            defined('HOST_NAME') ? HOST_NAME : '',
            '::csrf_secret::',
        ];
        $secret = hash('sha256', implode('|', $parts));
    }
    return $secret;
}

function csrfGetUserIdentity()
{
    if (isset($GLOBALS['auth']['id']) && intval($GLOBALS['auth']['id']) > 0) {
        return 'user:' . intval($GLOBALS['auth']['id']);
    }

    $ip = server('REMOTE_ADDR', 'unknown');
    $ua = server('HTTP_USER_AGENT', 'unknown');
    return 'guest:' . $ip . ':' . hash('sha256', $ua);
}

function csrfGenerateToken()
{
    $timestamp = time();
    $payload = $timestamp . ':' . csrfGetUserIdentity();
    $hmac = hash_hmac('sha256', $payload, csrfGetSecret());
    return $timestamp . '.' . $hmac;
}

function csrfGetToken()
{
    static $token = null;
    if ($token === null) {
        $token = csrfGenerateToken();
    }
    return $token;
}

function csrfValidateToken($token)
{
    if (!$token || strpos($token, '.') === false) {
        return false;
    }

    list($timestamp, $hmac) = explode('.', $token, 2);
    if (!ctype_digit($timestamp) || strlen($hmac) !== 64) {
        return false;
    }

    $timestamp = intval($timestamp);
    $now = time();
    $maxAge = 60 * 60 * 24;
    if (($now - $timestamp) > $maxAge) {
        return false;
    }
    if ($timestamp > ($now + 300)) {
        return false;
    }

    $payload = $timestamp . ':' . csrfGetUserIdentity();
    $expectedHmac = hash_hmac('sha256', $payload, csrfGetSecret());
    return hash_equals($expectedHmac, $hmac);
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . safeAttr(csrfGetToken()) . '">';
}

function csrfCheck(bool $debugMode = false)
{
    $token = post('csrf_token');

    if (csrfValidateToken($token)) {
        return true;
    }

    error_log('CSRF validation failed: ' . server('REQUEST_METHOD') . ' ' . server('REQUEST_URI') . ' ip=' . server('REMOTE_ADDR'));
    if ($debugMode) {
        return false;
    }

    http_response_code(403);
    die('CSRF token validation failed. Please refresh the page and try again.');
}

function csrfRegenerateToken()
{
    return csrfGenerateToken();
}

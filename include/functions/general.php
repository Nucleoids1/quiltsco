<?php
function cookie($name, mixed $default = '')
{
    if (!isset($_COOKIE[$name]) || is_array($_COOKIE[$name])) {
        return $default;
    }
    return $_COOKIE[$name];
}

function unsetCookie($name)
{
    unset($_COOKIE[$name]);
}

function cookieInt($name, int $default = 0)
{
    if (!isset($_COOKIE[$name]) || is_array($_COOKIE[$name])) {
        return $default;
    }
    $_value = filter_var($_COOKIE[$name], FILTER_VALIDATE_INT);
    return $_value !== false ? $_value : $default;
}

function files($name, mixed $default = null)
{
    return isset($_FILES[$name], $_FILES[$name]['size']) && $_FILES[$name]['size'] ? $_FILES[$name] : $default;
}


function redirectWithNotice($notice, $location)
{
    makeCookie('notice', $notice);
    header('Location: ' . $location);
    die;
}

function get($name, mixed $default = '')
{
    if (!isset($_GET[$name]) || is_array($_GET[$name])) {
        return $default;
    }
    return $_GET[$name];
}

function addBase64Padding($base64)
{
    $padding = (4 - strlen($base64) % 4) % 4;
    return str_pad($base64, strlen($base64) + $padding, '=', STR_PAD_RIGHT);
}

function encodeUrlPath($path)
{
    $encoded = base64_encode($path);
    return str_replace(['+', '/', '='], ['-', '_', ''], $encoded);
}

function decodeUrlPath($encoded, mixed $default = null)
{
    if ($encoded === null || $encoded === '') {
        return $default;
    }

    if (preg_match('/^[A-Za-z0-9_-]+$/', $encoded)) {
        $base64 = str_replace(['-', '_'], ['+', '/'], $encoded);
        $base64 = addBase64Padding($base64);

        $path = base64_decode($base64, true);
        if ($path !== false && $path !== '') {
            return $path;
        }
    }

    return $default;
}

function getInt($name, int $default = 0, bool $allowNegative = false)
{
    if (!isset($_GET[$name]) || is_array($_GET[$name])) {
        return $default;
    }
    $_value = filter_var($_GET[$name], FILTER_VALIDATE_INT);
    if ($_value === false || (!$allowNegative && $_value < 0)) {
        return $default;
    }
    return $_value;
}

function post($name, mixed $default = '')
{
    if (!isset($_POST[$name]) || is_array($_POST[$name])) {
        return $default;
    }
    return $_POST[$name];
}

function postInt($name, int $default = 0, bool $allowNegative = false)
{
    if (!isset($_POST[$name]) || is_array($_POST[$name])) {
        return $default;
    }
    $_value = filter_var($_POST[$name], FILTER_VALIDATE_INT);
    if ($_value === false || (!$allowNegative && $_value < 0)) {
        return $default;
    }
    return $_value;
}

function postArray($name, $default = array())
{
    if (!isset($_POST[$name]) || !is_array($_POST[$name])) {
        return $default;
    }
    return $_POST[$name];
}

function server($name, string $default = '')
{
    return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
}

function hasForwardedHostHeader(): bool
{
    return trim(server('HTTP_X_FORWARDED_HOST')) !== '';
}

function niceDate($datetime, string $params = 'D M j, Y @ g:ia', int $seconds = 0)
{
    return date($params, mktime(intval(substr($datetime, 11, 2)), intval(substr($datetime, 14, 2)), intval(substr($datetime, 17, 2)) + $seconds, intval(substr($datetime, 5, 2)), intval(substr($datetime, 8, 2)), intval(substr($datetime, 0, 4))));
}

function getUsername($id, $status = 0)
{
    $statusCode = '<span style="font-weight: bold;">&raquo;</span> ';
    $membersRow = (new \Databases\Members())->findById($id);
    if ($membersRow) {
        if ($status) {
            $membersLastonRow = (new \Databases\MembersLaston())->findByUserId($id);
            if ($membersLastonRow) {
                $seconds = (strtotime(date('Y-m-d H:i:s', server('REQUEST_TIME'))) - strtotime($membersLastonRow['laston']));
                if ($seconds < 300) {
                    $statusCode = '<span class="notice_good" style="font-weight: bold;">&raquo;</span> ';
                } elseif ($seconds < 600) {
                    $statusCode = '<span class="notice_attention" style="font-weight: bold;">&raquo;</span> ';
                } elseif ($seconds < 900) {
                    $statusCode = '<span class="notice_error" style="font-weight: bold;">&raquo;</span> ';
                }
            }
        }
        return ($status ? $statusCode : '') . ($status ? makeLink('?s=profile&u=' . $membersRow['username'], capitalize($membersRow['username'])) : capitalize($membersRow['username']));
    } else {
        return ($status ? $statusCode : '') . 'System';
    }
}

function capitalize($name)
{
    $name = strtolower($name);
    $name = ucwords($name);
    $name = join("'", array_map('ucwords', explode("'", $name)));
    $name = join("-", array_map('ucwords', explode("-", $name)));
    $name = join("_", array_map('ucwords', explode("_", $name)));
    $name = join("Mac", array_map('ucwords', explode("Mac", $name)));
    $name = join("Mc", array_map('ucwords', explode("Mc", $name)));
    return $name;
}
function getUserId($user)
{
    $membersRow = (new \Databases\Members())->findByUsername($user);
    if ($membersRow) {
        return $membersRow['id'];
    } else {
        return 0;
    }
}

function getMainImageId($id)
{
    $membersRow = (new \Databases\Members())->findById($id);
    if ($membersRow) {
        return $membersRow['main_image_id'];
    } else {
        return 0;
    }
}

function makeCacheCode()
{
    return random_int(intval('1' . str_repeat('0', strlen(strval(PHP_INT_MAX)) - 1)), PHP_INT_MAX);
}

function hasPermission($userId, $permission)
{
    $permissionsRow = (new \Databases\Permissions())->findByUserAndPermission($userId, $permission);
    if ($permissionsRow) {
        if ($permissionsRow['ip'] == '' || $permissionsRow['ip'] == server('REMOTE_ADDR')) {
            return true;
        }
    }
    return false;
}

function makeCookie($name, $value, $time = 365)
{
    if ($time) {
        $newtime = time() + 60 * 60 * 24 * $time;
    } else {
        $newtime = 0;
    }
    $domain = resolveCookieDomain();
    $secure = (strtolower(server('HTTPS')) === 'on' || server('HTTPS') === '1' || strtolower(server('HTTP_X_FORWARDED_PROTO')) === 'https');

    $cookieOptions = [
        'expires' => $newtime,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    if ($domain !== '') {
        $cookieOptions['domain'] = $domain;
    }

    setcookie($name, $value, $cookieOptions);
}

function killCookie($name)
{
    $domain = resolveCookieDomain();
    $secure = (strtolower(server('HTTPS')) === 'on' || server('HTTPS') === '1' || strtolower(server('HTTP_X_FORWARDED_PROTO')) === 'https');

    $cookieOptions = [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    if ($domain !== '') {
        $cookieOptions['domain'] = $domain;
    }

    setcookie($name, '', $cookieOptions);
}

function resolveCookieDomain()
{
    $host = trim(server('HTTP_HOST'));

    if ($host === '') {
        return '';
    }

    if (strpos($host, ',') !== false) {
        $host = trim(explode(',', $host)[0]);
    }

    $host = strtolower($host);

    if (strpos($host, ':') !== false && strpos($host, ']') === false) {
        $host = explode(':', $host)[0];
    }

    if (strpos($host, '[') === 0 && strpos($host, ']') !== false) {
        $host = trim($host, '[]');
    }

    if (strpos($host, 'www.') === 0) {
        $host = substr($host, 4);
    }

    if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
        return '';
    }

    return $host;
}

function language()
{
    if (isset($GLOBALS['auth']['language'])) {
        if ($GLOBALS['auth']['language'] == 'english' || $GLOBALS['auth']['language'] == 'french') {
            return $GLOBALS['auth']['language'];
        }
    }
    $language = (cookie('language') == 'english' || cookie('language') == 'french') ? cookie('language') : 'english';
    return $language;
}

function makeClickableRep($str, $bold = 1)
{
    $str = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1[url]\\2[/url]", $str);
    $str = preg_replace("#(^|[\n ])((www)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1[url]http://\\2[/url]", $str);
    if ($bold) {
        $str = preg_replace("/\[url\]((http|https|ftp|mailto):\/\/([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~ ]*?)\[\/url\]/is", "[<b><a href=\"$1\">$3</a></b>]", $str);
    } else {
        $str = preg_replace("/\[url\]((http|https|ftp|mailto):\/\/([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~ ]*?)\[\/url\]/is", "<a href=\"$1\">$3</a>", $str);
    }
    return $str;
}

function block($title, $information, $width = '', $top = 0, $padding = '', $coolBlock = '')
{
    if ($padding == '') {
        $padding = '3px';
    }
    $return = '';
    $return .= "\r\n\r\n";
    if ($coolBlock) {
        $return .= boxInsideTop();
    }
    $return .= '<div style="padding: ' . $padding . ';">';
    $return .= '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;"><tr>';
    if ($title != 'none') {
        if ($width) {
            $widthStyle = is_numeric($width) ? $width . 'px' : $width;
            $return .= '<td class="form_label_cell" style="width: ' . $widthStyle . ';">' . ($title ? $title . (strstr($title, '>') === false ? ':' : '') : '') . '</td>';
        } else {
            $return .= '<td class="form_label_cell">' . ($title ? $title . (strstr($title, '>') === false ? ':' : '') : '') . '</td>';
        }
    }
    $return .= '<td class="form_input_cell" style="vertical-align: top;">' . $information . '</td>';
    $return .= '</tr></table>';
    $return .= '</div>';
    if ($coolBlock) {
        $return .= boxInsideBottom();
    }
    return $return;
}

function safeAttr($text)
{
    if ($text === null) {
        $text = '';
    }
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Output helper context guide:
// - HTML text nodes: safeHtml()
// - HTML attributes: safeAttr()
// - URL attributes/hrefs/src: safeUrl()
// - JavaScript string context: safeJs()
function safeHtml($text)
{
    return safeAttr($text);
}

function safeUrl($url)
{
    if ($url === null) {
        return '';
    }
    if (!preg_match('~^(https?://|/|\?)~i', $url)) {
        $url = 'https://' . $url;
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function getCanonicalAppBaseUrl(): string
{
    $configuredBaseUrl = '';

    if (defined('APP_BASE_URL')) {
        $configuredBaseUrl = (string) APP_BASE_URL;
    }

    if ($configuredBaseUrl === '') {
        $envBaseUrl = getenv('APP_BASE_URL');
        if ($envBaseUrl !== false) {
            $configuredBaseUrl = (string) $envBaseUrl;
        }
    }

    if ($configuredBaseUrl === '' && defined('HOST_NAME')) {
        $configuredBaseUrl = (string) HOST_NAME;
    }

    $configuredBaseUrl = trim($configuredBaseUrl);
    $configuredBaseUrl = preg_replace('~^https?://~i', '', $configuredBaseUrl);

    if ($configuredBaseUrl === '') {
        return 'https://localhost';
    }

    return 'https://' . rtrim($configuredBaseUrl, '/');
}

function buildCanonicalAppUrl(string $path = '/'): string
{
    $baseUrl = getCanonicalAppBaseUrl();

    if ($path === '') {
        return $baseUrl;
    }

    if ($path[0] === '?') {
        return $baseUrl . '/' . $path;
    }

    return $baseUrl . '/' . ltrim($path, '/');
}

function safeJs($text)
{
    if ($text === null) {
        $text = '';
    }
    return json_encode($text, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

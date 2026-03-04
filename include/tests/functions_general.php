<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/general.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('general.php', ['cookie', 'unsetCookie', 'cookieInt', 'files', 'redirectWithNotice', 'get', 'addBase64Padding', 'encodeUrlPath', 'decodeUrlPath', 'getInt', 'post', 'postInt', 'postArray', 'server', 'hasForwardedHostHeader', 'niceDate', 'getUsername', 'capitalize', 'getUserId', 'getMainImageId', 'makeCacheCode', 'hasPermission', 'makeCookie', 'killCookie', 'resolveCookieDomain', 'language', 'makeClickableRep', 'block', 'safeAttr', 'safeHtml', 'safeUrl', 'getCanonicalAppBaseUrl', 'buildCanonicalAppUrl', 'safeJs']);

$_GET = ['name' => 'quilt', 'int' => '42', 'negative' => '-3', 'arr' => ['x']];
$_POST = ['title' => 'post title', 'count' => '9', 'negative' => '-8', 'items' => ['a', 'b']];
$_COOKIE = ['token' => 'abc123', 'age' => '7'];
$_FILES = ['upload' => ['size' => 123, 'name' => 'test.png'], 'empty' => ['size' => 0, 'name' => 'zero.png']];
$_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'example.test'];

assertSameValue('quilt', get('name', 'default'), 'get returns scalar query values.');
assertSameValue('default', get('arr', 'default'), 'get returns default for array query values.');
assertSameValue(42, getInt('int', 0), 'getInt parses integer query values.');
assertSameValue(0, getInt('negative', 0), 'getInt returns default for negative query values.');
assertSameValue(-3, getInt('negative', 0, true), 'getInt allows negative query values when explicitly enabled.');
assertSameValue('post title', post('title', 'default'), 'post returns scalar post values.');
assertSameValue(5, postInt('negative', 5), 'postInt returns default for negative post values.');
assertSameValue(-8, postInt('negative', 5, true), 'postInt allows negative post values when explicitly enabled.');
assertSameValue(['a', 'b'], postArray('items', []), 'postArray returns arrays.');
assertSameValue('abc123', cookie('token', 'default'), 'cookie reads scalar cookie values.');
assertSameValue(7, cookieInt('age', 0), 'cookieInt parses numeric cookies.');
assertSameValue('/gallery?a=1', decodeUrlPath(encodeUrlPath('/gallery?a=1'), 'fallback'), 'encode/decode URL path round trip works.');
assertSameValue('fallback', decodeUrlPath('', 'fallback'), 'decodeUrlPath returns default for empty input.');
assertSameValue('fallback', decodeUrlPath('@@@', 'fallback'), 'decodeUrlPath returns default for invalid base64-url input.');
assertSameValue('abc=', addBase64Padding('abc'), 'Base64 padding helper appends missing equals.');
assertSameValue('Alice-MacDonald', capitalize('alice-macdonald'), 'capitalize formats hyphenated names.');
assertTrue((bool) preg_match('/^[0-9]{18}$/', makeCacheCode()), 'makeCacheCode returns 18-digit numeric string.');
assertSameValue(['size' => 123, 'name' => 'test.png'], files('upload', null), 'files returns non-empty upload metadata.');
assertSameValue('fallback', files('empty', 'fallback'), 'files returns default for empty uploads.');
unsetCookie('token');
assertSameValue('default', cookie('token', 'default'), 'unsetCookie removes cookie key from superglobal.');
assertSameValue('Mon Jan 1, 2024 @ 12:00am', niceDate('2024-01-01 00:00:00'), 'niceDate formats expected readable date.');

// safeAttr tests
assertSameValue('&lt;script&gt;', safeAttr('<script>'), 'safeAttr escapes HTML tags.');
assertSameValue('&quot;test&quot;', safeAttr('"test"'), 'safeAttr escapes double quotes.');
assertSameValue('a&#039;b', safeAttr("a'b"), 'safeAttr escapes single quotes.');
assertSameValue('', safeAttr(null), 'safeAttr returns empty string for null.');
assertSameValue('normal', safeAttr('normal'), 'safeAttr leaves plain text unchanged.');

// safeUrl tests
assertSameValue('https://example.com', safeUrl('https://example.com'), 'safeUrl preserves https URLs.');
assertSameValue('http://example.com', safeUrl('http://example.com'), 'safeUrl preserves http URLs.');
assertSameValue('/path/to/page', safeUrl('/path/to/page'), 'safeUrl preserves absolute paths.');
assertSameValue('?query=1', safeUrl('?query=1'), 'safeUrl preserves query-only URLs.');
assertSameValue('https://example.com', safeUrl('example.com'), 'safeUrl prepends https:// to bare domains.');
assertSameValue('', safeUrl(null), 'safeUrl returns empty string for null.');
assertSameValue('https://test.com/path?q=&lt;a&gt;', safeUrl('https://test.com/path?q=<a>'), 'safeUrl escapes HTML in URLs.');
assertSameValue('?x=1&amp;y=2', safeUrl('?x=1&y=2'), 'safeUrl escapes ampersands in query strings.');


assertTrue(!hasForwardedHostHeader(), 'hasForwardedHostHeader is false when header is absent.');
$_SERVER['HTTP_X_FORWARDED_HOST'] = 'proxy.example';
assertTrue(hasForwardedHostHeader(), 'hasForwardedHostHeader is true when X-Forwarded-Host is present.');
$_SERVER['HTTP_HOST'] = 'www.example.test:443';
assertSameValue('example.test', resolveCookieDomain(), 'resolveCookieDomain only uses HTTP_HOST and normalizes host/port/www.');
$_SERVER['HTTP_HOST'] = 'localhost';
assertSameValue('', resolveCookieDomain(), 'resolveCookieDomain returns empty string for localhost.');
unset($_SERVER['HTTP_X_FORWARDED_HOST']);

// canonical app URL helpers
if (!defined('HOST_NAME')) {
    define('HOST_NAME', 'quiltsco.test');
}
assertSameValue('https://quiltsco.test', getCanonicalAppBaseUrl(), 'getCanonicalAppBaseUrl uses HOST_NAME with forced https.');
assertSameValue('https://quiltsco.test/?s=new_complete&c=abc', buildCanonicalAppUrl('?s=new_complete&c=abc'), 'buildCanonicalAppUrl joins query paths at root.');
assertSameValue('https://quiltsco.test/path/to/page', buildCanonicalAppUrl('/path/to/page'), 'buildCanonicalAppUrl joins absolute app paths.');

// safeJs tests
assertSameValue('"hello"', safeJs('hello'), 'safeJs encodes plain string for JS.');
assertSameValue('"\u003Cscript\u003E"', safeJs('<script>'), 'safeJs escapes HTML tags for JS context.');
assertSameValue('"it\u0027s"', safeJs("it's"), 'safeJs escapes single quotes for JS context.');
assertSameValue('"\u0026amp;"', safeJs('&amp;'), 'safeJs escapes ampersands for JS context.');
assertSameValue('""', safeJs(null), 'safeJs returns empty JSON string for null.');
assertSameValue('"a\u0026b"', safeJs('a&b'), 'safeJs escapes ampersands.');
assertSameValue('"line1\nline2"', safeJs("line1\nline2"), 'safeJs handles newlines.');

// language tests
unset($GLOBALS['auth']);
$_COOKIE = [];
assertSameValue('english', language(), 'language defaults to english when no auth or cookie.');
$_COOKIE = ['language' => 'french'];
assertSameValue('french', language(), 'language returns french from cookie.');
$_COOKIE = ['language' => 'english'];
assertSameValue('english', language(), 'language returns english from cookie.');
$_COOKIE = ['language' => 'invalid'];
assertSameValue('english', language(), 'language defaults to english for invalid cookie value.');
$GLOBALS['auth'] = ['language' => 'french'];
assertSameValue('french', language(), 'language returns french from auth global.');
$GLOBALS['auth'] = ['language' => 'english'];
assertSameValue('english', language(), 'language returns english from auth global.');
$GLOBALS['auth'] = ['language' => 'german'];
assertSameValue('english', language(), 'language falls back to cookie for invalid auth language.');
$GLOBALS['auth'] = [];
$_COOKIE = ['language' => 'french'];
assertSameValue('french', language(), 'language falls back to cookie when auth has no language.');
unset($GLOBALS['auth']);
$_COOKIE = [];
assertSameValue('&lt;script&gt;alert(1)&lt;/script&gt;', safeAttr('<script>alert(1)</script>'), 'safeAttr escapes HTML tags.');
assertSameValue('a&quot;b&#039;c', safeAttr('a"b\'c'), 'safeAttr escapes quotes.');
assertSameValue('', safeAttr(null), 'safeAttr handles null gracefully.');
assertSameValue('normal text', safeAttr('normal text'), 'safeAttr passes through safe text unchanged.');

finishTest('functions_general.php');

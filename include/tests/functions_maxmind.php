<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/general.php';
require_once __DIR__ . '/../functions/ip_encode.php';
require_once __DIR__ . '/../functions/maxmind.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('maxmind.php', ['ip2country', 'ip2flag', 'ip2locationDownload', 'ip2locationLoad', 'ip2locationFullDownload', 'ip2locationFullLoad', 'echoFlush']);

$_SERVER['REMOTE_ADDR'] = '8.8.8.8';
\DatabasesLocation\Ip2location::$row = ['country' => 'US', 'country_name' => 'United States'];
assertSameValue('US', ip2country(''), 'ip2country falls back to REMOTE_ADDR when IP argument is omitted.');

\DatabasesLocation\Ip2location::$row = null;
assertSameValue('', ip2country('1.2.3.4'), 'ip2country returns empty string when lookup has no row.');

\DatabasesLocation\Ip2location::$row = ['country' => 'ZZ', 'country_name' => 'Unknownland'];
assertSameValue('<img src="images/flags/unknown.png" alt="Unknown country flag" title="Unknown" />', ip2flag('1.2.3.4'), 'ip2flag falls back to unknown image when flag asset is absent.');

ob_start();
echoFlush('Working');
$out = ob_get_clean();
assertSameValue('Working<br />', $out, 'echoFlush appends HTML line break suffix.');

finishTest('functions_maxmind.php');

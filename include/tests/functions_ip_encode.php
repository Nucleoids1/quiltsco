<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/ip_encode.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('ip_encode.php', ['encodeIp', 'ipEncode']);

assertSameValue(3232235777, ipEncode('192.168.1.1'), 'ipEncode converts dotted IP to integer.');
assertSameValue(3232235777, encodeIp('192.168.1.1'), 'encodeIp matches ipEncode output.');
assertSameValue(0, ipEncode(''), 'ipEncode returns 0 for empty input.');
assertSameValue(2130706433, ipEncode('127.0.0.1'), 'Loopback IP converts to expected integer.');
assertSameValue(4294967295, ipEncode('255.255.255.255'), 'Max IPv4 converts to max 32-bit integer.');

finishTest('functions_ip_encode.php');

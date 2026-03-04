<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/ip_decode.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('ip_decode.php', ['decodeIp']);

assertSameValue('192.168.1.1', decodeIp(3232235777), 'decodeIp converts integer to dotted address.');
assertSameValue('0.0.0.0', decodeIp(0), 'decodeIp handles zero integer.');
assertSameValue(0, decodeIp(''), 'decodeIp returns 0 on empty input.');
assertSameValue('255.255.255.255', decodeIp(4294967295), 'decodeIp supports max 32-bit integer.');
assertSameValue('127.0.0.1', decodeIp(2130706433), 'decodeIp supports loopback integer.');

finishTest('functions_ip_decode.php');

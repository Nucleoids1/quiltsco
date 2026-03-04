<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/coolness.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('coolness.php', ['coolness']);

assertSameValue(0, coolness(123), 'coolness currently returns 0 due short-circuit.');
assertSameValue(0, coolness(123, 1), 'coolness still returns 0 with stats flag.');

finishTest('functions_coolness.php');

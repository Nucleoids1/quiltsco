<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/valid_username.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('valid_username.php', ['isValidUsername']);

assertSameValue(1, isValidUsername('Alice_1990'), 'Alphanumeric and underscore are accepted.');
assertSameValue(0, isValidUsername('9alice'), 'Must start with letter.');
assertSameValue(1, isValidUsername('Ab1_'), 'Minimal matching username passes.');
assertSameValue(0, isValidUsername('Ab1'), 'Too-short username fails.');
assertSameValue(1, isValidUsername('A-1z'), 'Hyphen and alnum in tail are accepted.');
assertSameValue(1, isValidUsername('Z.9__name'), 'Dot/underscore rich tail is accepted.');
assertSameValue(0, isValidUsername('Aa!z'), 'Illegal punctuation in third position is rejected.');

finishTest('functions_valid_username.php');

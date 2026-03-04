<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/closest_word.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('closest_word.php', ['closestWord']);

assertSameValue('This is a longer...', closestWord('This is a longer sentence to trim', 16), 'Trim respects word boundary and appends ellipsis.');
assertSameValue('Short text', closestWord('Short text', 20), 'Short strings are unchanged.');
assertSameValue('NoSpacesButLongWord', closestWord('NoSpacesButLongWord', 5), 'Long words without spaces remain unchanged.');
assertSameValue('A1 B2 C3...', closestWord('A1 B2 C3 D4', 8), 'Alphanumeric boundaries are accepted.');
assertSameValue('word...', closestWord('word punctuation ???', 8), 'Nearest preceding word is used when cut lands in punctuation run.');
assertSameValue('Two words...', closestWord('Two words here', 10), 'Mid-word limits trim to prior whole word.');

finishTest('functions_closest_word.php');

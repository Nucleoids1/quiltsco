<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/word_wrap_new.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('word_wrap_new.php', ['wordWrapNew']);

assertSameValue('visit <a href="http://example.com">http://example.com</a> now', wordWrapNew('visit example.com now', 0, 50), 'wordWrapNew linkifies bare .com domains.');
assertSameValue('contact <a href="mailto:">me@test.com</a>', wordWrapNew('contact me@test.com', 0, 50), 'wordWrapNew wraps emails in mailto anchors.');
assertSameValue('line one line two', wordWrapNew("line one\nline two", 0, 50), 'wordWrapNew flattens newlines when nl2br disabled.');
assertSameValue('word', wordWrapNew('  word  ', 0, 50), 'wordWrapNew trims repeated surrounding spaces.');
assertContains('[<a href="http://', wordWrapNew('http://example.com/' . str_repeat('a', 80), 0, 50), 'Long links are replaced by compact [link] marker.');
assertContains("\r\n", wordWrapNew("a\nb", 1, 50), 'nl2br mode preserves line breaks as CRLF markers.');

finishTest('functions_word_wrap_new.php');

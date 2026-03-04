<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/rw_code.php';

if (!function_exists('makeClickableRep')) {
    function makeClickableRep($string)
    {
        return $string;
    }
}
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('rw_code.php', ['quotes', 'replaceEmbededTags', 'smileys', 'imagesCode', 'bbcodes', 'addlinks']);

$simple = replaceEmbededTags('A [quote]B[/quote] C');
assertContains('<div style="border: 1px solid; padding: 1em; border-radius: 7px;">B</div>', $simple, 'replaceEmbededTags replaces simple quote pair.');

$nested = replaceEmbededTags('[quote]x[quote]y[/quote]z[/quote]');
assertSameValue(2, substr_count($nested, '<div style="border: 1px solid; padding: 1em; border-radius: 7px;">'), 'replaceEmbededTags preserves nesting depth.');

$quoted = quotes('[quote]', '[/quote]', 'A [quote]B[/quote] C');
assertContains('<table style="width: 100%; border-collapse: collapse; background: #;">', $quoted, 'quotes wraps quoted content in table markup.');

$underlined = bbcodes('[u]Under[/u]');
assertContains('<span style="text-decoration: underline;">Under</span>', $underlined, 'bbcodes converts underline BBCode to CSS-based span.');

$sanitized = bbcodes('<span style="color:red">X</span>');
assertFalse(strpos($sanitized, 'style=') !== false, 'bbcodes strips user-provided style attributes.');

finishTest('functions_rw_code.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_name_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_name_valid.php', ['communityNameValid']);

if (!defined('COMMUNITY_NAME_MIN')) {
    define('COMMUNITY_NAME_MIN', 3);
}
if (!defined('COMMUNITY_NAME_MAX')) {
    define('COMMUNITY_NAME_MAX', 12);
}

assertSameValue('You must give your community a name.', communityNameValid(''), 'Empty community name is rejected.');
assertSameValue('Your community\'s name must be at least 3 characters long.', communityNameValid('ab'), 'Too-short community name is rejected.');
assertSameValue('Your community\'s name must be at most 12 characters long.', communityNameValid('abcdefghijklmn'), 'Too-long community name is rejected.');
assertSameValue('Your community\'s name cannot have non alpha-numeric characters. (hyphens (-), underscores (_) and periods (.) are allowed)', communityNameValid('abc!'), 'Invalid punctuation is rejected.');
assertSameValue(null, communityNameValid('Group_01'), 'Valid community name returns null.');

finishTest('functions_community_name_valid.php');

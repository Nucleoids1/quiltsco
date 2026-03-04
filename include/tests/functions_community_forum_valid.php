<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_forum_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_forum_valid.php', ['communityForumValid']);

if (!defined('COMMUNITY_FORUM_NAME_MIN')) define('COMMUNITY_FORUM_NAME_MIN', 3);
if (!defined('COMMUNITY_FORUM_NAME_MAX')) define('COMMUNITY_FORUM_NAME_MAX', 10);
if (!defined('COMMUNITY_FORUM_DESC_MIN')) define('COMMUNITY_FORUM_DESC_MIN', 5);
if (!defined('COMMUNITY_FORUM_DESC_MAX')) define('COMMUNITY_FORUM_DESC_MAX', 15);

assertSameValue('You must give your forum a name.', communityForumValid('', 'abcdef'), 'Forum requires a name.');
assertSameValue('Your forum\'s name must be at least 3 characters long.', communityForumValid('ab', 'abcdef'), 'Forum name min length enforced.');
assertSameValue('Your forum\'s name must be at most 10 characters long.', communityForumValid('abcdefghijk', 'abcdef'), 'Forum name max length enforced.');
assertSameValue('Your forum\' description must be at least 5 characters long.', communityForumValid('valid', 'abcd'), 'Forum description min length enforced.');
assertSameValue('Your forum\'s description must be at most 15 characters long.', communityForumValid('valid', 'abcdefghijklmnop'), 'Forum description max length enforced.');
assertSameValue('', communityForumValid('valid', 'good desc'), 'Valid forum values return empty string.');

finishTest('functions_community_forum_valid.php');

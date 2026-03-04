<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_reply_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_reply_valid.php', ['communityReplyValid']);

if (!defined('COMMUNITY_REPLY_BODY_MAX')) define('COMMUNITY_REPLY_BODY_MAX', 10);

assertSameValue('Your reply must have a body', communityReplyValid(''), 'Reply requires a body.');
assertSameValue('Your reply cannot be more than 10 characters long', communityReplyValid('abcdefghijk'), 'Reply max length enforced.');
assertSameValue(null, communityReplyValid('abc'), 'Valid reply body returns null.');

finishTest('functions_community_reply_valid.php');

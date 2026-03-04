<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_update_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_update_valid.php', ['communityUpdateValid']);

if (!defined('COMMUNITY_REPLY_BODY_MAX')) define('COMMUNITY_REPLY_BODY_MAX', 10);

assertSameValue('Your update must have a body', communityUpdateValid(''), 'Update requires a body.');
assertSameValue('Your update cannot be more than 10 characters long', communityUpdateValid('abcdefghijk'), 'Update max length enforced.');
assertSameValue(null, communityUpdateValid('hello'), 'Valid update returns null.');

finishTest('functions_community_update_valid.php');

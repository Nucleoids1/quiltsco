<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_thread_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_thread_valid.php', ['communityThreadValid']);

if (!defined('COMMUNITY_THREAD_TITLE_MAX')) define('COMMUNITY_THREAD_TITLE_MAX', 6);
if (!defined('COMMUNITY_THREAD_BODY_MAX')) define('COMMUNITY_THREAD_BODY_MAX', 12);

assertSameValue('Your message must have a subject', communityThreadValid('', 'body'), 'Thread requires subject.');
assertSameValue('Your subject cannot be more than 6 characters long', communityThreadValid('1234567', 'body'), 'Thread title max length enforced.');
assertSameValue('Your message must have a body', communityThreadValid('title', ''), 'Thread requires body.');
assertSameValue('Your message cannot be more than 12 characters long', communityThreadValid('title', '1234567890123'), 'Thread body max length enforced.');
assertSameValue(null, communityThreadValid('title', 'body'), 'Valid thread values return null.');

finishTest('functions_community_thread_valid.php');

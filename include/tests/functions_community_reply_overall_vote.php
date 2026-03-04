<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_reply_overall_vote.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_reply_overall_vote.php', ['replyOverallRating']);

\Databases\CommunityMessagesRating::$counts = ['1' => 5, '-1' => 2];
assertSameValue(4, replyOverallRating(10), 'reply_overall_rating returns base 1 plus vote delta.');
\Databases\CommunityMessagesRating::$counts = ['1' => 0, '-1' => 3];
assertSameValue(-2, replyOverallRating(10), 'reply_overall_rating can return negative values.');

finishTest('functions_community_reply_overall_vote.php');

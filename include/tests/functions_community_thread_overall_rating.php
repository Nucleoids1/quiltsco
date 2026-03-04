<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_thread_overall_rating.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_thread_overall_rating.php', ['threadOverallRating']);

\Databases\CommunityThreadsRatings::$count = 0;
assertSameValue('Unrated [0]', threadOverallRating(1), 'No ratings returns Unrated.');

\Databases\CommunityThreadsRatings::$count = 5;
\Databases\CommunityThreadsRatings::$positiveRows = [['thread_id' => 1], ['thread_id' => 1], ['thread_id' => 1]];
\Databases\CommunityThreadsRatings::$groupedRow = ['category_id' => 2];
\Databases\CommunityThreadsCategories::$categories = [2 => ['name' => 'Helpful', 'positive' => 1]];
assertSameValue('<span class="notice_good">Helpful [1]</span>', threadOverallRating(1), 'Positive dominant ratings render good badge and score.');

\Databases\CommunityThreadsRatings::$count = 4;
\Databases\CommunityThreadsRatings::$positiveRows = [['thread_id' => 1]];
\Databases\CommunityThreadsRatings::$groupedRow = ['category_id' => 3];
\Databases\CommunityThreadsCategories::$categories = [3 => ['name' => 'Spam', 'positive' => 0]];
assertSameValue('<span class="notice_error">Spam [-2]</span>', threadOverallRating(1), 'Negative dominant ratings render error badge and negative score.');

finishTest('functions_community_thread_overall_rating.php');

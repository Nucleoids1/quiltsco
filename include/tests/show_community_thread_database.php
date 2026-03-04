<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_thread.php', ['CommunityMessages::countByThreadId' => 1, 'CommunityMessages::countByThreadIdUpToMessage' => 1, 'CommunityMessages::findFirstByThreadIdAfterMessage' => 1, 'CommunityMessages::findLatestByThreadId' => 1, 'CommunityMessages::selectPageByThreadId' => 1, 'CommunityMessagesBodies::selectPrimaryKey' => 2, 'CommunityMessagesRating::hasVoteByMessageAndUser' => 1, 'CommunityMessagesUpdates::selectByMessageId' => 1, 'CommunityThreads::findThreadContextForDisplay' => 1, 'CommunityThreads::updateWhere' => 1, 'CommunityThreadsCategories::selectAllByName' => 1, 'CommunityThreadsPointers::findByUserAndThread' => 1, 'CommunityThreadsPointers::updateReadPointer' => 1, 'CommunityThreadsRatings::findByThreadAndUser' => 1, 'Ignored::isIgnoredByUser' => 1, 'MembersMoods::selectPrimaryKey' => 1, 'Tiles::selectPrimaryKey' => 1]);

finishTest('show_community_thread_database.php');

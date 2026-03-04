<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_my_forum.php', ['Community::selectPrimaryKey' => 1, 'CommunityForums::selectActiveBySection' => 1, 'CommunityMessages::countByThreadIdAfterMessage' => 1, 'CommunityMessages::countByUserAndThreadId' => 1, 'CommunityMessages::findLatestByThreadId' => 1, 'CommunitySections::selectActiveByCommunity' => 1, 'CommunityThreads::selectRecentByForum' => 1, 'CommunityThreadsPointers::findByUserAndThread' => 1]);

finishTest('show_community_my_forum_database.php');

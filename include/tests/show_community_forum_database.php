<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_forum.php', ['CommunityForums::findActiveForumContext' => 1, 'CommunityMessages::countByThreadIdAfterMessage' => 1, 'CommunityMessages::countByUserAndThreadId' => 1, 'CommunityMessages::findLatestByThreadId' => 1, 'CommunityThreads::selectPageByForum' => 1, 'CommunityThreadsPointers::findByUserAndThread' => 1, 'Quilts::selectPrimaryKey' => 1, 'Tiles::selectPrimaryKey' => 1]);

finishTest('show_community_forum_database.php');

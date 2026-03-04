<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community.php', ['Community::selectPrimaryKey' => 1, 'CommunityForums::selectActiveBySectionOrdered' => 1, 'CommunityMessages::countByThreadIdBetweenMessages' => 1, 'CommunityPermissions::selectDistinctUserIdsByCommunity' => 1, 'CommunitySections::selectActiveByCommunityOrdered' => 1, 'CommunityThreads::selectRecentByForum' => 1, 'CommunityThreadsPointers::findByUserAndThread' => 1]);

finishTest('show_community_database.php');

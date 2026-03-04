<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_thread_move.php', ['CommunityForums::selectActiveBySectionOrdered' => 1, 'CommunitySections::selectActiveByCommunityOrdered' => 1, 'CommunityThreads::findThreadContextForDisplay' => 1]);

finishTest('show_community_thread_move_database.php');

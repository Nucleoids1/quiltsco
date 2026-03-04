<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_forum_modify.php', ['CommunityForums::findActiveForumContextInActiveSection' => 1, 'CommunityForumsPermissions::selectByForumAndUser' => 1, 'CommunityForumsPermissions::selectDistinctUserIdsByForum' => 1]);

finishTest('show_community_forum_modify_database.php');

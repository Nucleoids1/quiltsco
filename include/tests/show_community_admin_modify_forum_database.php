<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_admin_modify_forum.php', ['CommunityForums::findActiveForumContextInActiveSection' => 1, 'CommunityForumsPermissions::findByForumAndUser' => 1, 'CommunityForumsPermissions::hasPermissionByForumAndUser' => 1, 'CommunityForumsPermissions::selectDistinctPermissions' => 1]);

finishTest('show_community_admin_modify_forum_database.php');

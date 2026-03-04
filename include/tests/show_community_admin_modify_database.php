<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_admin_modify.php', ['Community::selectPrimaryKey' => 1, 'CommunityPermissions::findByCommunityAndUser' => 1, 'CommunityPermissions::hasPermissionByCommunityAndUser' => 1, 'CommunityPermissions::selectDistinctPermissionsExcept' => 1]);

finishTest('show_community_admin_modify_database.php');

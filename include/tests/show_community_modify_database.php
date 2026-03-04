<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_modify.php', ['Community::selectPrimaryKey' => 1, 'CommunityBannedIps::selectByCommunityOrdered' => 1, 'CommunityBannedUsers::selectByCommunityOrdered' => 1, 'CommunityForums::countActiveBySection' => 1, 'CommunityPermissions::selectDistinctPermissions' => 1, 'CommunityPermissions::selectPermissionsByCommunityAndUser' => 1, 'CommunitySections::selectActiveByCommunityOrdered' => 1, 'StatsIps::selectByIpWithKnownUsers' => 1, 'StatsIps::selectByUserOrderedIp' => 1]);

finishTest('show_community_modify_database.php');

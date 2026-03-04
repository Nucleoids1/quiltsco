<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_admin_modify_section.php', ['CommunitySections::findActiveSectionContext' => 1, 'CommunitySectionsPermissions::findBySectionAndUser' => 1, 'CommunitySectionsPermissions::hasPermissionBySectionAndUser' => 1, 'CommunitySectionsPermissions::selectDistinctPermissions' => 1]);

finishTest('show_community_admin_modify_section_database.php');

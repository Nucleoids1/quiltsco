<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_section_modify.php', ['CommunityForums::selectActiveBySectionOrdered' => 1, 'CommunitySections::findActiveSectionContext' => 1, 'CommunitySectionsPermissions::selectBySectionAndUser' => 1, 'CommunitySectionsPermissions::selectDistinctUserIdsBySection' => 1]);

finishTest('show_community_section_modify_database.php');

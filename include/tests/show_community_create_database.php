<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_create.php', ['CommunityPermissions::sqlRead' => 1]);

finishTest('show_community_create_database.php');

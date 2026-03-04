<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('members.php', ['Members::count' => 1, 'Members::sqlRead' => 2, 'Members::sqlReadRow' => 1, 'MembersExtras::selectWhereRow' => 1]);

finishTest('show_members_database.php');

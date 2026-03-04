<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('friends.php', ['Friends::count' => 1, 'Friends::selectWhere' => 1]);

finishTest('show_friends_database.php');

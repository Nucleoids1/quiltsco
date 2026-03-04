<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('password_complete.php', ['MembersCreate::selectWhereRow' => 1]);

finishTest('show_password_complete_database.php');

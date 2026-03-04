<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('approvals.php', ['Quilts::selectPrimaryKey' => 1, 'Tiles::sqlRead' => 1, 'Tiles::sqlReadRow' => 1]);

finishTest('show_approvals_database.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tiles_completed.php', ['Quilts::selectPrimaryKey' => 1, 'Tiles::count' => 1, 'Tiles::selectWhere' => 1]);

finishTest('show_tiles_completed_database.php');

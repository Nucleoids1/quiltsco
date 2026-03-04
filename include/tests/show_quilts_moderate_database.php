<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('quilts_moderate.php', ['Quilts::selectPrimaryKey' => 1, 'QuiltsPermissions::selectWhere' => 1, 'Tiles::count' => 2, 'TilesPending::count' => 1]);

finishTest('show_quilts_moderate_database.php');

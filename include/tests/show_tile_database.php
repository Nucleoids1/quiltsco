<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tile.php', ['Quilts::selectPrimaryKey' => 1, 'QuiltsPermissions::selectWhere' => 1, 'Tiles::selectWhereRow' => 1]);

finishTest('show_tile_database.php');

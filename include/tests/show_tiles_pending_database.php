<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tiles_pending.php', ['Quilts::selectPrimaryKey' => 1, 'TilesPending::count' => 1, 'TilesPending::selectWhereRow' => 1]);

finishTest('show_tiles_pending_database.php');

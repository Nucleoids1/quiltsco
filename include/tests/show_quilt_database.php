<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('quilt.php', ['Quilts::selectPrimaryKey' => 1, 'Tiles::count' => 1, 'Tiles::sqlRead' => 1, 'TilesPending::selectWhereRow' => 1]);

finishTest('show_quilt_database.php');

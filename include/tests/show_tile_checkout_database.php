<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tile_checkout.php', ['Quilts::selectPrimaryKey' => 1, 'TilesPending::checkoutTile' => 1]);

finishTest('show_tile_checkout_database.php');

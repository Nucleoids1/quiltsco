<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('quilts.php', ['Quilts::count' => 1, 'Quilts::selectWhere' => 1]);

finishTest('show_quilts_database.php');

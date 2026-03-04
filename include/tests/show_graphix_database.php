<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('graphix.php', ['Images::count' => 1, 'Images::selectWhere' => 1]);

finishTest('show_graphix_database.php');

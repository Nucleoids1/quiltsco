<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('image.php', ['Images::selectPrimaryKey' => 1, 'Images::selectWhere' => 2]);

finishTest('show_image_database.php');

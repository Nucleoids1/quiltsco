<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('upload_image.php', ['GalleryImages::count' => 1, 'GalleryImages::selectWhere' => 1, 'Members::selectPrimaryKey' => 1]);

finishTest('show_upload_image_database.php');

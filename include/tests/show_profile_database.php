<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('profile.php', ['Friends::selectWhereRow' => 1, 'GalleryImages::selectWhere' => 2, 'Members::selectPrimaryKey' => 1, 'MembersExtras::selectPrimaryKey' => 1, 'MembersLaston::selectPrimaryKey' => 1, 'Quilts::selectPrimaryKey' => 1, 'Tiles::count' => 1, 'Tiles::selectWhere' => 3, 'Tiles::sqlReadRow' => 1]);

finishTest('show_profile_database.php');

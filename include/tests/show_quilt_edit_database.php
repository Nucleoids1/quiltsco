<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('quilt_edit.php', ['Quilts::selectPrimaryKey' => 1, 'QuiltsInvites::sqlRead' => 1, 'QuiltsPermissions::count' => 1, 'QuiltsPermissions::sqlRead' => 1]);

finishTest('show_quilt_edit_database.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('new_thanks.php', []);

finishTest('show_new_thanks_database.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('security_code.php', ['SecurityCodeCache::sqlWrite' => 1, 'SecurityCodeLast::sqlWrite' => 1]);

finishTest('show_security_code_database.php');

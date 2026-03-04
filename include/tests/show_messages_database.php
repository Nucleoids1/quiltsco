<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('messages.php', ['Members::selectPrimaryKey' => 2, 'Messages::count' => 1, 'Messages::selectPrimaryKey' => 2, 'Messages::sqlRead' => 1, 'Messages::sqlReadRow' => 1, 'Messages::updateWhere' => 1, 'MessagesEmail::deleteWhere' => 1, 'MessagesIndex::sqlRead' => 1, 'MessagesIndex::sqlReadRow' => 1, 'SecurityCodeLast::deleteWhere' => 1, 'SecurityCodeLast::replaceArray' => 1]);

finishTest('show_messages_database.php');

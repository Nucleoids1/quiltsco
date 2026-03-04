<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('communities.php', ['Community::selectWhere' => 1, 'CommunityForums::sqlReadRow' => 1]);

finishTest('show_communities_database.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('community_smileys.php', ['CommunitySmileys::selectAllByName' => 1]);

finishTest('show_community_smileys_database.php');

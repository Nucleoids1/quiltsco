<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tracker_bugs_info.php', ['TrackerBugs::selectPrimaryKey' => 1, 'TrackerBugs::sqlWrite' => 1, 'TrackerBugsCategories::selectPrimaryKey' => 1, 'TrackerBugsImages::selectWhere' => 1, 'TrackerBugsStatus::selectPrimaryKey' => 1]);

finishTest('show_tracker_bugs_info_database.php');

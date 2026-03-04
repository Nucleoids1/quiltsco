<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('tracker_bugs_admin.php', ['TrackerBugs::count' => 2, 'TrackerBugs::selectPrimaryKey' => 1, 'TrackerBugsCategories::selectWhere' => 1, 'TrackerBugsImages::selectWhere' => 1, 'TrackerBugsStatus::selectWhere' => 1]);

finishTest('show_tracker_bugs_admin_database.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_show_database_test_helpers.php';

assertShowDatabaseFileContract('userinfo.php', ['Community::selectPrimaryKey' => 1, 'CommunityPermissions::selectWhere' => 1, 'GeoCities::selectWhere' => 1, 'GeoCountries::selectWhere' => 1, 'GeoRegions::selectWhere' => 1, 'Members::selectPrimaryKey' => 1, 'MembersCreate::selectWhereRow' => 1, 'MembersExtras::selectWhereRow' => 1]);

finishTest('show_userinfo_database.php');

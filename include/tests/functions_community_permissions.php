<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_permissions.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_permissions.php', ['communityPermissions']);

$GLOBALS['auth'] = ['id' => 5, 'community' => []];
\Databases\CommunityPermissions::$hasRoot = 0;
\Databases\CommunityPermissions::$userPerms = ['post'];
\Databases\CommunitySectionsPermissions::$perms = ['moderator'];
\Databases\CommunityForumsPermissions::$perms = [];

communityPermissions(2, 10, 20);
assertSameValue(false, $GLOBALS['auth']['community']['administrator'], 'administrator false when no root and no explicit permission.');
assertSameValue(true, $GLOBALS['auth']['community']['moderator'], 'Section permissions elevate moderator to true.');
assertSameValue(true, $GLOBALS['auth']['community']['post'], 'Direct community permission is honored.');

\Databases\CommunityPermissions::$hasRoot = 1;
\Databases\CommunityPermissions::$userPerms = [];
\Databases\CommunitySectionsPermissions::$perms = [];
communityPermissions(2, 10, 20);
assertSameValue(true, $GLOBALS['auth']['community']['moderator'], 'Root permission grants all discovered permissions.');

finishTest('functions_community_permissions.php');

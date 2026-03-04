<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_banned.php', ['communityBanned']);

finishTest('functions_community_banned.php');

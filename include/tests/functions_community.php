<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community.php', ['updateCommunityThread', 'updateCommunitySection', 'updateCommunityForum', 'updateAllCommunity']);

finishTest('functions_community.php');

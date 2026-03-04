<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('tile_is_available.php', ['tileIsAvailable']);

finishTest('functions_tile_is_available.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('tile_get_sides.php', ['tileGetSides']);

finishTest('functions_tile_get_sides.php');

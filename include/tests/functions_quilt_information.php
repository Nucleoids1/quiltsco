<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('quilt_information.php', ['quiltInformation']);

finishTest('functions_quilt_information.php');

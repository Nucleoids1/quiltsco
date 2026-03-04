<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('create_quilt_image_cache.php', ['createQuiltImageCache']);

finishTest('functions_create_quilt_image_cache.php');

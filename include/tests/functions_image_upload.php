<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('image_upload.php', ['safeZipImageName', 'processUploads', 'processFile', 'shrinkImage', 'thumbImage']);

finishTest('functions_image_upload.php');

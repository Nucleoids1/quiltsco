<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/image_directory.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('image_directory.php', ['imageDirectory', 'binariesPath']);

assertSameValue('00000001/', imageDirectory(1), 'imageDirectory starts first bucket at 1.');
assertSameValue('00000001/', imageDirectory(5000), 'imageDirectory keeps boundary value inside first bucket.');
assertSameValue('00005001/', imageDirectory(5001), 'imageDirectory advances bucket past boundary.');
assertSameValue('00010001', binariesPath(10001), 'binariesPath mirrors bucket logic without slash.');
assertSameValue('00000011/', imageDirectory(20, 10), 'Custom bucket size affects directory lower bound.');
assertSameValue('00000011', binariesPath(20, 10), 'Custom bucket size affects binary directory lower bound.');

finishTest('functions_image_directory.php');

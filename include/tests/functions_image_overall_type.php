<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/image_overall_type.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('image_overall_type.php', ['imageOverallType', 'imageOverallTypeId', 'imageOverallTypeWorksafe']);

\Databases\ImagesCategoriesRating::$voteCount = 0;
assertSameValue('No Type [0]', imageOverallType(1), 'No votes returns No Type label.');
assertSameValue(0, imageOverallTypeId(1), 'No votes returns type id 0.');
assertSameValue(true, imageOverallTypeWorksafe(1), 'No votes defaults to worksafe true.');

\Databases\ImagesCategoriesRating::$voteCount = 8;
\Databases\ImagesCategoriesRating::$topTwo = [['num' => 5, 'category_id' => 2], ['num' => 3, 'category_id' => 1]];
\Databases\ImagesCategoriesRating::$topOne = ['num' => 5, 'category_id' => 2];
\Databases\ImagesCategories::$map = [2 => ['name' => 'NSFW', 'worksafe' => 0], 1 => ['name' => 'Nature', 'worksafe' => 1]];

assertSameValue('<span class="notice_error">NSFW [2]</span>', imageOverallType(1), 'Top category renders name and difference with unsafe class.');
assertSameValue(2, imageOverallTypeId(1), 'Top category id is returned.');
assertSameValue(false, imageOverallTypeWorksafe(1), 'Unsafe top category returns false.');

finishTest('functions_image_overall_type.php');

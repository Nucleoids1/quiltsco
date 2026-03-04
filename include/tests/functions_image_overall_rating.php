<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/image_overall_rating.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('image_overall_rating.php', ['imageOverallRating']);

\Databases\ImagesRating::$counts = ['1' => 3, '-1' => 3];
assertSameValue('Neutral', imageOverallRating(1), 'Equal vote counts produce Neutral.');
\Databases\ImagesRating::$counts = ['1' => 6, '-1' => 2];
assertSameValue('<span class="notice_good">Good [+4]</span>', imageOverallRating(1), 'More good votes produce positive summary.');
\Databases\ImagesRating::$counts = ['1' => 1, '-1' => 5];
assertSameValue('<span class="notice_error">Bad [-4]</span>', imageOverallRating(1), 'More bad votes produce negative summary.');

finishTest('functions_image_overall_rating.php');

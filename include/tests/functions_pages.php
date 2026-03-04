<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/pages.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('pages.php', ['pages', 'pagesAjax']);

$smallPages = pages('?s=list', 2, 4, false);
assertContains('<b>2</b>', $smallPages, 'pages highlights current page.');
assertContains('?s=list&p=1', $smallPages, 'pages includes first link.');
assertContains('?s=list&p=4', $smallPages, 'pages includes last link in small range mode.');

$largePages = pages('?s=list', 10, 25, true);
assertContains(' .. ', $largePages, 'pages uses ellipsis when page range is truncated.');
assertContains('Next &#187;&#187;', $largePages, 'pages adds Next link when requested.');
assertContains('?s=list&p=25', $largePages, 'pages includes the last page in large range mode.');

$lastPage = pages('?s=list', 25, 25, true);
assertSameValue(false, strpos($lastPage, 'Next &#187;&#187;') !== false, 'pages omits Next link on last page.');

$ajaxPages = pagesAjax('/items?page=%%PAGE%%', 3, 12, true);
assertContains('/items?page=4', $ajaxPages, 'pagesAjax substitutes %%PAGE%% in links.');
assertContains('/items?page=1', $ajaxPages, 'pagesAjax first-page link is present.');

finishTest('functions_pages.php');

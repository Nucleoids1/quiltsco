<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/community_section_valid.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('community_section_valid.php', ['communitySectionValid']);

if (!defined('COMMUNITY_SECTION_NAME_MIN')) define('COMMUNITY_SECTION_NAME_MIN', 2);
if (!defined('COMMUNITY_SECTION_NAME_MAX')) define('COMMUNITY_SECTION_NAME_MAX', 8);

assertSameValue('You must give your section a name.', communitySectionValid(''), 'Section requires a name.');
assertSameValue('Your section\'s name must be at least 2 characters long.', communitySectionValid('a'), 'Section min length enforced.');
assertSameValue('Your section\'s name must be at most 8 characters long.', communitySectionValid('abcdefghi'), 'Section max length enforced.');
assertSameValue(null, communitySectionValid('general'), 'Valid section name returns null.');

finishTest('functions_community_section_valid.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/members_create_rate_limit.php';
require_once __DIR__ . '/_functions_database_stubs.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('members_create_rate_limit.php', ['membersCreateThrottleCheck']);

\Databases\MembersCreate::$emailCount = 1;
\Databases\MembersCreate::$ipCount = 0;
assertSameValue(true, membersCreateThrottleCheck('x@example.com', '1.2.3.4'), 'Email reuse in last 10 minutes is throttled.');

\Databases\MembersCreate::$emailCount = 0;
\Databases\MembersCreate::$ipCount = 3;
assertSameValue(true, membersCreateThrottleCheck('', '1.2.3.4'), 'Three or more IP attempts are throttled.');

\Databases\MembersCreate::$emailCount = 0;
\Databases\MembersCreate::$ipCount = 2;
assertSameValue(false, membersCreateThrottleCheck('', '1.2.3.4'), 'Below IP threshold is allowed.');
assertTrue(isset(\Databases\MembersCreate::$deletedWhere['posted_on <']), 'Old records cleanup condition is issued before checks.');

finishTest('functions_members_create_rate_limit.php');

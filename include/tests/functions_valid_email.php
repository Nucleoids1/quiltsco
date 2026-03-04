<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/valid_email.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('valid_email.php', ['isValidEmail']);

assertSameValue(1, isValidEmail('user.name-42@example-domain.com'), 'Valid structured email passes.');
assertSameValue(0, isValidEmail('not-an-email'), 'Missing @ fails.');
assertSameValue(1, isValidEmail('a@bb.cd'), '2-character TLD passes.');
assertSameValue(0, isValidEmail('name@domain.c'), '1-character TLD fails.');
assertSameValue(0, isValidEmail('@domain.com'), 'Missing local part fails.');
assertSameValue(1, isValidEmail('u_ser@sub_domain.example.com'), 'Underscores in local/domain parts pass regex.');
assertSameValue(0, isValidEmail('name@domain.toolongg'), 'TLD longer than 6 chars fails.');
assertSameValue(0, isValidEmail('name@@domain.com'), 'Double @ fails validation.');

finishTest('functions_valid_email.php');

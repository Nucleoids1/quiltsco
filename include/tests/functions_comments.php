<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('comments.php', ['comments']);

finishTest('functions_comments.php');

<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('image_voting.php', ['imageVoting']);

finishTest('functions_image_voting.php');

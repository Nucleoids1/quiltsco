<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/filename_extension.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('filename_extension.php', ['filenameExtension']);

assertSameValue('jpg', filenameExtension('photo.JPG'), 'Uppercase extension is lowercased.');
assertSameValue('gz', filenameExtension('archive.tar.gz'), 'Only final extension segment is returned.');
assertSameValue(false, filenameExtension('README'), 'No-dot filename returns false.');
assertSameValue('htaccess', filenameExtension('.htaccess'), 'Dotfile extension is parsed.');
assertSameValue('', filenameExtension('file.'), 'Trailing period returns empty extension.');
assertSameValue('123', filenameExtension('archive.123'), 'Numeric extension is returned as string.');
assertSameValue('php', filenameExtension('script.PHP'), 'Mixed-case extensions are normalized to lowercase.');

finishTest('functions_filename_extension.php');

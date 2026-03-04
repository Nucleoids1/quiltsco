<?php

declare(strict_types=1);

require_once __DIR__ . '/../closest_word.php';
require_once __DIR__ . '/../filename_extension.php';
require_once __DIR__ . '/../valid_email.php';
require_once __DIR__ . '/../valid_username.php';

$failures = [];

function assertSameValue($expected, $actual, string $message): void
{
    global $failures;

    if ($expected !== $actual) {
        $failures[] = sprintf(
            "%s\nExpected: %s\nActual:   %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        );
    }
}

// filenameExtension
assertSameValue('jpg', filenameExtension('photo.JPG'), 'filenameExtension lowercases uppercase extensions.');
assertSameValue('gz', filenameExtension('archive.tar.gz'), 'filenameExtension uses the substring after the final period.');
assertSameValue(false, filenameExtension('README'), 'filenameExtension returns false when no period is present.');
assertSameValue('htaccess', filenameExtension('.htaccess'), 'filenameExtension handles dotfiles with a leading period.');
assertSameValue('', filenameExtension('file.'), 'filenameExtension returns an empty string for names ending with a period.');
assertSameValue('mp3', filenameExtension('video.mp3'), 'filenameExtension keeps numeric extensions.');
assertSameValue('jpeg', filenameExtension('family.photo.JpEg'), 'filenameExtension lowercases mixed-case extensions.');
assertSameValue('', filenameExtension('.'), 'filenameExtension returns an empty string for a filename that is only a period.');
assertSameValue('local', filenameExtension('.env.local'), 'filenameExtension returns the portion after the final period for dotted dotfiles.');

// closestWord
assertSameValue('This is a longer...', closestWord('This is a longer sentence to trim', 16), 'closestWord trims at a word boundary and appends ellipsis.');
assertSameValue('Short text', closestWord('Short text', 20), 'closestWord returns original text under max length.');
assertSameValue('NoSpacesButLongWord', closestWord('NoSpacesButLongWord', 5), 'closestWord keeps original text when there are no spaces to trim on.');
assertSameValue('Alpha beta...', closestWord('Alpha beta gamma', 12), 'closestWord trims to previous complete word when limit cuts into a later word.');
assertSameValue('A1 B2 C3...', closestWord('A1 B2 C3 D4', 8), 'closestWord accepts alphanumeric characters at the trim boundary.');
assertSameValue('One two...', closestWord('One two three', 8), 'closestWord trims exactly at a whitespace boundary before the next word.');
assertSameValue('Leading 1number...', closestWord('Leading 1number suffix words', 18), 'closestWord keeps the nearest trailing alphanumeric before appending ellipsis.');
assertSameValue('EdgeCase', closestWord('EdgeCase', 8), 'closestWord keeps text unchanged when its length equals the max limit.');

// isValidEmail
assertSameValue(1, isValidEmail('user.name-42@example-domain.com'), 'isValidEmail accepts punctuation in the local part and domain.');
assertSameValue(0, isValidEmail('not-an-email'), 'isValidEmail rejects values without an @ and domain.');
assertSameValue(1, isValidEmail('a@bb.cd'), 'isValidEmail accepts minimal valid structure and 2-char TLD.');
assertSameValue(0, isValidEmail('name@domain.c'), 'isValidEmail rejects 1-character TLDs.');
assertSameValue(0, isValidEmail('@domain.com'), 'isValidEmail requires at least one starting alphanumeric before @.');
assertSameValue(1, isValidEmail('user@sub_domain.example.com'), 'isValidEmail allows underscore in domain labels due regex character class.');
assertSameValue(0, isValidEmail('user@domain.toolongg'), 'isValidEmail rejects TLDs longer than 6 characters.');
assertSameValue(0, isValidEmail('user+tag@example.com'), 'isValidEmail rejects plus signs because + is not in the local-part character class.');
assertSameValue(0, isValidEmail('user@-domain.com'), 'isValidEmail rejects domains that begin with a hyphen because the first domain char must be alphanumeric.');
assertSameValue(1, isValidEmail('name@example.abcdef'), 'isValidEmail accepts a 6-character TLD at the configured upper bound.');

// isValidUsername
assertSameValue(1, isValidUsername('Alice_1990'), 'isValidUsername accepts alphanumeric usernames with underscores.');
assertSameValue(0, isValidUsername('9alice'), 'isValidUsername rejects usernames that start with non-letters.');
assertSameValue(1, isValidUsername('Ab1_'), 'isValidUsername accepts a minimal 4-character username that matches the regex.');
assertSameValue(0, isValidUsername('Ab1'), 'isValidUsername rejects 3-character usernames because regex requires one extra trailing char.');
assertSameValue(1, isValidUsername('A-1z'), 'isValidUsername allows any second character due dot wildcard in regex.');
assertSameValue(0, isValidUsername('Aa!z'), 'isValidUsername rejects invalid third characters not in [A-z0-9].');
assertSameValue(1, isValidUsername('Z.9__name'), 'isValidUsername allows dot, underscore, and letters in the tail section.');
assertSameValue(1, isValidUsername('Ab9-._tail'), 'isValidUsername accepts hyphen and period characters in the tail class.');
assertSameValue(0, isValidUsername('Aa'), 'isValidUsername rejects 2-character usernames that are too short for the pattern.');
assertSameValue(0, isValidUsername('Aa?tail'), 'isValidUsername rejects question marks because they are not in the allowed tail class.');

if (!empty($failures)) {
    fwrite(STDERR, "Unit test failures:\n\n" . implode("\n\n", $failures) . "\n");
    exit(1);
}

echo "All unit tests passed.\n";

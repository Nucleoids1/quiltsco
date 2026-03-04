<?php

declare(strict_types=1);

function passwordSecurityRequirementsText(): string
{
    return 'Your password is not secure. It must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
}

function validatePasswordForAuthFlow(?string $pass1, ?string $pass2): ?string
{
    if (!$pass1) {
        return 'You need to enter a password.';
    }

    if (!$pass2) {
        return 'You need to re-enter your password.';
    }

    if ($pass1 !== $pass2) {
        return 'Your passwords do not match.';
    }

    if (strlen($pass1) < PASSWORD_MIN) {
        return 'Your password is not long enough. Your password must be at least ' . PASSWORD_MIN . ' characters long.';
    }

    if (strlen($pass1) > PASSWORD_MAX) {
        return 'Your password is too long. Your password cannot be more than ' . PASSWORD_MAX . ' characters long.';
    }

    if (!isPasswordSecure($pass1)) {
        return passwordSecurityRequirementsText();
    }

    return null;
}

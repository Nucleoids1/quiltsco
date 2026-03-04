<?php
    require_once('../include/functions/password_policy.php');

    $_cache = get('cache');
    $_pass1 = post('pass1');
    $_pass2 = post('pass2');

    $tokenTtlSeconds = defined('MEMBERS_CREATE_TOKEN_TTL_SECONDS') ? MEMBERS_CREATE_TOKEN_TTL_SECONDS : 60 * 60 * 24;
    $membersCreateSince = date('Y-m-d H:i:s', server('REQUEST_TIME') - $tokenTtlSeconds);

    $membersCreateRow = (new \Databases\MembersCreate())->findValidByCache($_cache, $membersCreateSince);
    if (!$membersCreateRow)
    {
        makeCookie('notice', 'This link has expired. Please request a new password reset email and try again.');
        header('Location: ./?s=password');
        die;
    }

    $membersRow = (new \Databases\Members())->findByEmail($membersCreateRow['email']);
    if (!$membersRow)
    {
        makeCookie('notice', 'Sorry, the email address you are trying to change your password for is invalid.');
        header('Location: ./?s=password');
        die;
    }

    $passwordError = validatePasswordForAuthFlow($_pass1, $_pass2);
    if ($passwordError !== null)
    {
        $GLOBALS['notice'] = $passwordError;
        include('../include/show/password_complete.php');
        die;
    }

    (new \Databases\Members())->updatePasswordByEmail($membersCreateRow['email'], password_hash($_pass1, PASSWORD_DEFAULT));
    (new \Databases\MembersCreate())->deleteByEmail($membersCreateRow['email']);
    createMemberOnlineSession($membersRow['id']);

    header('Location: ./?s=userinfo');
    die;

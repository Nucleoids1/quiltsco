<?php
    require_once('../include/functions/password_policy.php');
    require_once('../include/functions/valid_username.php');

    $_cache = post('cache');
    $_name = post('name');
    $_pass1 = post('pass1');
    $_pass2 = post('pass2');

    $tokenTtlSeconds = defined('MEMBERS_CREATE_TOKEN_TTL_SECONDS') ? MEMBERS_CREATE_TOKEN_TTL_SECONDS : 60 * 60 * 24;
    $membersCreateSince = date('Y-m-d H:i:s', server('REQUEST_TIME') - $tokenTtlSeconds);

    if (!$_name)
    {
        makeCookie('notice', 'You did not enter a username.');
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }
    elseif (strlen($_name) < USERNAME_MIN)
    {
        makeCookie('notice', 'Your username is not long enough. Your username must be at least ' . USERNAME_MIN . ' characters long.');
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }
    elseif (strlen($_name) > USERNAME_MAX)
    {
        makeCookie('notice', 'Your username is too long. Your username cannot be more than ' . USERNAME_MAX . ' characters long.');
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }
    elseif ((new \Databases\Members())->countByUsername($_name))
    {
        makeCookie('notice', 'Sorry, that username is already in use.');
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }
    elseif (!isValidUsername($_name))
    {
        makeCookie('notice', 'Your username is not valid. Please only use alpha-numerica characters.');
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }

    $passwordError = validatePasswordForAuthFlow($_pass1, $_pass2);
    if ($passwordError !== null)
    {
        makeCookie('notice', $passwordError);
        header('Location: ./?s=new_complete&c=' . $_cache);
        die;
    }

    $membersCreateRow = (new \Databases\MembersCreate())->findValidByCache($_cache, $membersCreateSince);
    if ($membersCreateRow)
    {
        $email = $membersCreateRow['email'];
    }
    else
    {
        makeCookie('notice', 'This link has expired. Please request a new account confirmation email and try again.');
        header('Location: ./?s=new');
        die;
    }

    if ((new \Databases\Members())->countByEmail($email))
    {
        makeCookie('notice', 'Sorry, that email address is already in use.');
        header('Location: ./?s=new');
        die;
    }

    $passwordHash = password_hash($_pass1, PASSWORD_DEFAULT);

    $mysqlInsertId = (new \Databases\Members())->createMember($_name, $passwordHash, $email);
    (new \Databases\MembersExtras())->createExtras($mysqlInsertId);
    (new \Databases\MembersLaston())->createLaston($mysqlInsertId);
    (new \Databases\MembersCreate())->deleteByEmail($email);


    header('Location: ./?s=userinfo');
    die;

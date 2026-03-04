<?php
    require_once('../include/functions/members_create_rate_limit.php');
    require_once('../include/functions/send_email.php');
    require_once('../include/functions/valid_email.php');
    require_once('../include/functions/valid_username.php');

    $_email = post('email');
    $_old = post('old');
    $_pass1 = post('pass1');
    $_pass2 = post('pass2');
    $_userName = post('username');

    $errors = '';

    $membersRow = (new \Databases\Members())->findById($GLOBALS['auth']['id']);
    if ($membersRow && !verifyPassword($_old, $membersRow['password']))
    {
        $membersRow = null;
    }
    if ($membersRow)
    {
        if ($_userName && $_userName != $membersRow['username'])
        {
            if (strlen($_userName) < USERNAME_MIN)
            {
                $errors .= 'Your username must be at least ' . USERNAME_MIN . ' characters long.<br />';
            }
            elseif (strlen($_userName) > USERNAME_MAX)
            {
                $errors .= 'Your username cannot be more than ' . USERNAME_MAX . ' characters long.<br />';
            }
            elseif ((new \Databases\Members())->countByUsername($_userName))
            {
                $errors .= 'Sorry, that username is already in use.<br />';
            }
            elseif (!isValidUsername($_userName))
            {
                $errors .= 'Your username is not valid. Please only use alpha-numerica characters.<br />';
            }
        }
        if ($_pass1 || $_pass2)
        {
            if (!$_pass1)
            {
                $errors .= 'You need to enter a password.<br />';
            }
            if (!$_pass2)
            {
                $errors .= 'You need to re-enter your password.<br />';
            }
            if ($_pass1 && $_pass2 && $_pass1 != $_pass2)
            {
                $errors .= 'Your passwords do not match.<br />';
            }
            elseif (strlen($_pass1) < PASSWORD_MIN)
            {
                $errors .= 'Your password must be at least ' . PASSWORD_MIN . ' characters long.<br />';
            }
            elseif (strlen($_pass1) > PASSWORD_MAX)
            {
                $errors .= 'Your password cannot be more than ' . PASSWORD_MAX . ' characters long.<br />';
            }
        }
        if ($_email && $_email != $membersRow['email'])
        {
            if ((new \Databases\Members())->countByEmail($_email))
            {
                $errors .= 'Sorry, that email address is already in use.<br />';
            }
            if ((new \Databases\MembersCreate())->countByEmail($_email))
            {
                $errors .= 'Sorry, that email address is already used for a pending account.<br />';
            }
        }

        if ($errors)
        {
            makeCookie('notice', $errors);
        }
        else
        {
            if ($_userName && $_userName != $membersRow['username'])
            {
                (new \Databases\Members())->updateUsername($GLOBALS['auth']['id'], $_userName);
            }
            if ($_pass1 || $_pass2)
            {
                $passwordHash = password_hash($_pass1, PASSWORD_DEFAULT);
                (new \Databases\Members())->updatePassword($GLOBALS['auth']['id'], $passwordHash);
                makeCookie('login_password', $passwordHash);
            }
            if ($_email && $_email != $membersRow['email'])
            {
                $ip = substr(server('REMOTE_ADDR'), 0, 15);
                if (membersCreateThrottleCheck($_email, $ip))
                {
                    makeCookie('notice', 'Too many email validation requests. Please wait 10 minutes and try again.<br />');
                    header('Location: /?s=userinfo');
                    die;
                }

                $cache = md5(time() . chr(mt_rand(97, 122)) . chr(mt_rand(97, 122)) . chr(mt_rand(97, 122)) . chr(mt_rand(97, 122)));
                (new \Databases\MembersCreate())->deleteByUserId($GLOBALS['auth']['id']);
                (new \Databases\MembersCreate())->createPendingAccount($_email, $GLOBALS['auth']['id'], $cache, $ip);
                $body = "You have requested an email change on your account with the Quilts Community. In order to continue, please use the the following code in your userinfo on the site:\r\n\r\nCODE: " . $cache . "\r\n\r\nIf you did not request this change, please do nothing, we will not email you again.";
                sendEmail($_email, 'Quilts Community - Email Validation', $body);
            }
        }
    }
    else
    {
        makeCookie('notice', 'You must enter the correct old password to change any of your new information.<br />');
    }

    header('Location: /?s=userinfo');
    die;

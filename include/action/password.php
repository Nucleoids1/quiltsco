<?php
    require_once('../include/functions/members_create_rate_limit.php');
    require_once('../include/functions/send_email.php');
    require_once('../include/functions/valid_email.php');
    require_once('../include/functions/security_code.php');

    $_email = post('email');
    $_securityCode = post('security_code');
    $ip = substr(server('REMOTE_ADDR'), 0, 15);
    $genericNotice = 'If the email is valid, we sent instructions.';


    if (!isSecurityCodeValid($_securityCode))
    {
        makeCookie('notice', 'The security code you entered was incorrect.');
        header('Location: ./?s=password');
        die;
    }

    if (!$_email || !isValidEmail($_email))
    {
        error_log('[password] non-committal response: invalid email format for email=' . var_export($_email, true) . ' ip=' . $ip);
        makeCookie('notice', $genericNotice);
        header('Location: ./?s=password_thanks');
        die;
    }

    if (membersCreateThrottleCheck($_email, $ip))
    {
        error_log('[password] non-committal response: rate limit hit for email=' . $_email . ' ip=' . $ip);
        makeCookie('notice', $genericNotice);
        header('Location: ./?s=password_thanks');
        die;
    }

    if (!(new \Databases\Members())->countByEmail($_email))
    {
        error_log('[password] non-committal response: no account found for email=' . $_email . ' ip=' . $ip);
        makeCookie('notice', $genericNotice);
        header('Location: ./?s=password_thanks');
        die;
    }

    $cache = bin2hex(random_bytes(32));
    (new \Databases\MembersCreate())->upsertByEmail($_email, $cache, $ip);
    $number = substr_count(str_replace('www.', '', strtolower(server('HTTP_HOST'))), '.');
    $url = 'https://' . ($number == 1 ? 'www.' : '') . str_replace('www.', '', strtolower(server('HTTP_HOST'))) . '/?s=password_complete&cache=' . $cache;
    $body = "You have requested a password update for your account since you seem to have misplaced yours. In order to continue please click the following link:\r\n\r\n\t".$url."\r\n\r\nIf you actually know your password, please do nothing, we will not email you again.";
    sendEmail($_email, 'Quilts Community - Forgot Password', $body);
    error_log('[password] reset email sent for email=' . $_email . ' ip=' . $ip);

    makeCookie('notice', $genericNotice);
    header('Location: ./?s=password_thanks');
    die;

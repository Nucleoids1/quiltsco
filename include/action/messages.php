<?php
    require_once('../include/functions/send_email.php');
    require_once('../include/functions/security_code.php');

    $_body = post('body');
    $_securityCode = post('security_code');
    $_user = get('u');

    $userId = getUserId($_user);
    if (!$userId)
    {
        makeCookie('notice', 'Sorry, we do not have any members by that name.');
        header('Location: ./?s=messages');
        die;
    }

    $userCountry = ip2country();

    $err = '';

    $messagesRow = (new \Databases\Messages())->findLastBySenderId($GLOBALS['auth']['id']);
    if ($messagesRow)
    {
        if ($messagesRow['body'] == $_body)
        {
            $err = 'double_post';
        }
        elseif ($messagesRow['posted_on'] > date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s') - 10, date('m'), date('d'), date('Y'))))
        {
            $err = 'rapid';
        }
    }

    if ($userCountry != 'CA' && $userCountry != 'US')
    {
        if (!isSecurityCodeValid($_securityCode))
        {
            $err = 'security';
        }
    }

    if ($err == '' && $_body == '')
    {
        $err = 'body_missing';
    }

    if ($err != '')
    {
        header('Location: ./?s=messages&u=' . $_user . '&e=' . $err);
        die;
    }

    $mysqlInsertId = (new \Databases\Messages())->sendMessage($GLOBALS['auth']['id'], $userId, $_body);

    $messagesIndexRow = (new \Databases\MessagesIndex())->findBySenderAndReceiver($GLOBALS['auth']['id'], $userId);
    if ($messagesIndexRow)
    {
        (new \Databases\MessagesIndex())->updateLastReceived($GLOBALS['auth']['id'], $userId, date('Y-m-d H:i:s', server('REQUEST_TIME')), $mysqlInsertId);
    }
    else
    {
        (new \Databases\MessagesIndex())->trackMessageReceived($GLOBALS['auth']['id'], $userId, $mysqlInsertId);
    }

    $messagesIndexRow = (new \Databases\MessagesIndex())->findByReceiverAndSender($GLOBALS['auth']['id'], $userId);
    if ($messagesIndexRow)
    {
        (new \Databases\MessagesIndex())->updateLastSent($GLOBALS['auth']['id'], $userId, date('Y-m-d H:i:s', server('REQUEST_TIME')), $mysqlInsertId);
    }
    else
    {
        (new \Databases\MessagesIndex())->trackMessageSent($GLOBALS['auth']['id'], $userId, $mysqlInsertId);
    }

    $membersExtrasRow = (new \Databases\MembersExtras())->findByUserId($userId);
    if ($membersExtrasRow)
    {
        $messagesEmailRow = (new \Databases\MessagesEmail())->findByUserIdAndLinkId($userId, $GLOBALS['auth']['id']);
        if (!$messagesEmailRow)
        {
            $membersRow = (new \Databases\Members())->findById($userId);
            $body = 'You have a new message from ' . getUsername($GLOBALS['auth']['id']) . '.';
            sendEmail($membersRow['email'], 'Quilts Community - New Message', $body);
            (new \Databases\MessagesEmail())->trackEmailNotification($userId, $GLOBALS['auth']['id']);
        }
    }


    header('Location: ./?s=messages&u=' . $_user);
    die;

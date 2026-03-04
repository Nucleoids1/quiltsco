<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_reply_valid.php');
    require_once('../include/functions/community_thread_valid.php');

    $_backPath = decodeUrlPath(get('b'));
    $_messageBody = post('body');
    $_messageId = getInt('i');
    $_threadTitle = post('title');

    $communityMessagesRow = (new \Databases\CommunityMessages())->findMessageContext($_messageId);
    if (!$communityMessagesRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityMessagesRow['community_id']);
    communityPermissions($communityMessagesRow['community_id']);

    $allowReply = $GLOBALS['auth']['id'] && ($communityMessagesRow['forum_locked'] == 0 || $GLOBALS['auth']['community']['locked_locked_post']) && ($communityMessagesRow['thread_locked'] == 0 || $GLOBALS['auth']['community']['thread_locked_post']);
    $allowModify = 0;
    $allowModifyTitle = 0;
    if ($allowReply)
    {
        $lastMessageRow = (new \Databases\CommunityMessages())->findLatestByThreadId($communityMessagesRow['thread_id']);
        if ($lastMessageRow)
        {
            if ($GLOBALS['auth']['id'] == $lastMessageRow['message_user_id'])
            {
                $ago = date('Y-m-d H:i:s', mktime(date("H"), date("i") - 25, date("s"), date('m'), date('d'), date('Y')));
                if ($lastMessageRow['message_posted_on'] >= $ago)
                {
                    $allowModify = $lastMessageRow['message_id'];
                    if ($communityMessagesRow['thread_first_message_id'] == $lastMessageRow['message_id'])
                    {
                        $allowModifyTitle = $communityMessagesRow['thread_title'];
                    }
                }
            }
        }
    }

    if ($allowModify && $allowModifyTitle)
    {
        $error = communityThreadValid($_threadTitle, $_messageBody);
        if ($error)
        {
            makeCookie('notice', $error);
            header('Location: ./?s=communities');
            die;
        }
        (new \Databases\CommunityThreads())->updateTitle($communityMessagesRow['thread_id'], $_threadTitle);
        (new \Databases\CommunityMessagesBodies())->updateBody($communityMessagesRow['message_id'], $_messageBody);
    }
    elseif ($allowModify)
    {
        $error = communityReplyValid($_messageBody);
        if ($error)
        {
            makeCookie('notice', $error);
            header('Location: ./?s=communities');
            die;
        }
        (new \Databases\CommunityMessagesBodies())->updateBody($communityMessagesRow['message_id'], $_messageBody);
    }

    header('Location: ./?' . ($_backPath ? $_backPath : 's=community_thread&i=' . $communityMessagesRow['thread_id']));
    die;

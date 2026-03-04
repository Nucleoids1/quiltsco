<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_reply_valid.php');
    require_once('../include/functions/ip_encode.php');

    $_back = decodeUrlPath(get('b'));
    $_body  = post('body');
    $_id = get('i');

    $communityRow = (new \Databases\CommunityThreads())->findThreadContext($_id);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, that thread does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    if ($communityRow['thread_locked'] && $communityRow['forum_locked'] && !$GLOBALS['auth']['community']['thread_lock_post'])
    {
        makeCookie('notice', 'Sorry, that thread is locked. No Posting allowed.');
        header('Location: ./?s=communities');
        die;
    }

    $communityMessagesRow = (new \Databases\CommunityMessages())->findLatestByThreadId($_id);
    if ($communityMessagesRow)
    {
        if ($GLOBALS['auth']['id'] == $communityMessagesRow['message_user_id'])
        {
            makeCookie('notice', 'Sorry, you can not reply twice in a row.');
            header('Location: ./?s=communities');
            die;
        }
    }

    $error = communityReplyValid($_body);
    if ($error)
    {
        makeCookie('notice', $error);
        header('Location: ./?s=communities');
        die;
    }

    $membersMoodsRow = (new \Databases\MembersMoods())->findLatestByUser($GLOBALS['auth']['id']);
    if ($membersMoodsRow)
    {
        $mood = $membersMoodsRow['id'];
    }
    else
    {
        $mood = 0;
    }

    $id = (new \Databases\CommunityMessages())->addReply(
        $communityRow['thread_id'],
        $GLOBALS['auth']['id'],
        encodeIp(server('REMOTE_ADDR')),
        $mood
    );
    (new \Databases\CommunityMessagesBodies())->addMessageBody($id, $_body);
    (new \Databases\CommunityThreadsPointers())->upsertPointer($communityRow['thread_id'], $GLOBALS['auth']['id'], $id);

    $messages = 0;
    $communityMessagesCount = (new \Databases\CommunityMessages())->countByThreadId($communityRow['thread_id']);
    if ($communityMessagesCount)
    {
        $messages = $communityMessagesCount;
    }
    (new \Databases\CommunityThreads())->updateLastMessage($communityRow['thread_id'], $GLOBALS['auth']['id'], date('Y-m-d H:i:s', server('REQUEST_TIME')), $messages, $id);

    $forumMessages = (new \Databases\CommunityThreads())->sumMessagesByForum($communityRow['forum_id']);
    (new \Databases\CommunityForums())->updateForumMessages($communityRow['forum_id'], $forumMessages);

    header('Location: ./?' . ($_back ? $_back : 's=community_forum&i=' . $communityRow['forum_id']));

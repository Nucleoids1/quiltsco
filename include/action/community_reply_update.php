<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_reply_valid.php');

    $_messageBody = post('body');
    $_messageId = get('i');

    $communityMessagesRow = (new \Databases\CommunityMessages())->findMessageContext($_messageId);
    if (!$communityMessagesRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityMessagesRow['community_id']);
    communityPermissions($communityMessagesRow['community_id']);

    $error = communityReplyValid($_messageBody);
    if ($error)
    {
        makeCookie('notice', $error);
        header('Location: ./?s=communities');
        die;
    }

    $communityMessageRow = (new \Databases\CommunityMessages())->findById($_messageId);
    if ($communityMessageRow)
    {
        if (($GLOBALS['auth']['community']['modify_own_message'] && $GLOBALS['auth']['id'] == $communityMessageRow['message_user_id']) || $GLOBALS['auth']['community']['modify_any_message'])
        {
            (new \Databases\CommunityMessagesUpdates())->addUpdate(
                $_messageId,
                $GLOBALS['auth']['id'],
                $_messageBody,
                encodeIp(server('REMOTE_ADDR'))
            );
        }
    }

    $previousCount = (new \Databases\CommunityMessages())->countByThreadIdBeforeMessage($communityMessagesRow['thread_id'], $communityMessagesRow['message_id']);
    $page = ceil(($previousCount + 1) / COMMUNITY_REPLIES_PER_PAGE);

    header('Location: ./?s=community_thread&i=' . $communityMessagesRow['thread_id'] . '&p=' . $page . '#r' . $communityMessagesRow['message_id']);

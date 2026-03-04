<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');

    $_backPath = decodeUrlPath(get('b'));
    $_messageId = get('i');

    $communityMessagesRow = (new \Databases\CommunityMessages())->findMessageContext($_messageId);
    if (!$communityMessagesRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityMessagesRow['community_id']);
    communityPermissions($communityMessagesRow['community_id']);

    $forumId = $communityMessagesRow['forum_id'];
    $threadId = $communityMessagesRow['thread_id'];

    if ($GLOBALS['auth']['community']['delete_message'])
    {
        if ($communityMessagesRow['message_id'] != $communityMessagesRow['thread_first_message_id'])
        {
            (new \Databases\CommunityMessagesUpdates())->deleteByMessageId($communityMessagesRow['message_id']);
            (new \Databases\CommunityMessagesRating())->deleteByMessageId($communityMessagesRow['message_id']);
            (new \Databases\CommunityMessagesBodies())->deleteByMessageId($communityMessagesRow['message_id']);
            (new \Databases\CommunityMessages())->deleteById($communityMessagesRow['message_id']);
            $messages = 0;
            $communityMessagesCount = (new \Databases\CommunityMessages())->countByThreadId($threadId);
            if ($communityMessagesCount)
            {
                $messages = $communityMessagesCount;
            }
            $communityMessagesLatestRow = (new \Databases\CommunityMessages())->findLatestByThreadId($threadId);
            if ($communityMessagesLatestRow)
            {
                (new \Databases\CommunityThreads())->updateLastMessage(
                    $threadId,
                    $communityMessagesLatestRow['message_user_id'],
                    $communityMessagesLatestRow['message_posted_on'],
                    $messages,
                    $communityMessagesLatestRow['message_id']
                );
            }
            $forumMessages = (new \Databases\CommunityThreads())->sumMessagesByForum($forumId);
            (new \Databases\CommunityForums())->updateForumMessages($forumId, $forumMessages);
        }
    }

    header('Location: ./?' . ($_backPath ? $_backPath : 's=community_thread&i=' . $threadId . '#l'));

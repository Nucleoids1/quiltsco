<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');

    $_backPath = decodeUrlPath(get('b'));
    $_threadId = getInt('i');

    $communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_threadId);
    if (!$communityRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    $forumId = $communityRow['forum_id'];

    if ($GLOBALS['auth']['community']['thread_delete'])
    {
        (new \Databases\CommunityMessagesUpdates())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityMessagesRating())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityMessagesBodies())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityMessages())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityThreads())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityThreadsPointers())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityThreadsRatings())->deleteByThreadId($communityRow['thread_id']);
        (new \Databases\CommunityForums())->updateForumStats(
            $forumId,
            (new \Databases\CommunityThreads())->sumMessagesByForum($forumId),
            (new \Databases\CommunityThreads())->countByForum($forumId)
        );
    }

    header('Location: ./?' . ($_backPath ? $_backPath : 's=community_forum&i=' . $forumId));

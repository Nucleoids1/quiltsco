<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');

    $_backPath = decodeUrlPath(get('b'));
    $_newForumId = getInt('x');
    $_threadId = getInt('i');

    $communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_threadId);
    if (!$communityRow)
    {
        makeCookie('notice', 'This thread does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    if (!$GLOBALS['auth']['community']['thread_move'])
    {
        makeCookie('notice', 'You do not have access to move messages from this forum.');
        header('Location: ./?s=community_forum&i=' . $communityRow['forum_id']);
        die;
    }

    $communityForumsRow = (new \Databases\CommunityForums())->findForumContextInCommunity($_newForumId, $communityRow['community_id']);
    if (!$communityForumsRow)
    {
        makeCookie('notice', 'This thread does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    communityPermissions($communityForumsRow['community_id'], $communityForumsRow['section_id'], $communityForumsRow['forum_id']);

    if (!$GLOBALS['auth']['community']['thread_move'])
    {
        makeCookie('notice', 'You do not have access to move messages to this forum.');
        header('Location: ./?s=community_forum&i=' . $communityForumsRow['forum_id']);
        die;
    }

    (new \Databases\CommunityThreads())->moveForum($communityRow['thread_id'], $communityForumsRow['forum_id']);
    $array = array($communityRow['forum_id'], $communityForumsRow['forum_id']);
    foreach ($array AS $forumId)
    {
        (new \Databases\CommunityForums())->updateForumStats(
            $forumId,
            (new \Databases\CommunityThreads())->sumMessagesByForum($forumId),
            (new \Databases\CommunityThreads())->countByForum($forumId)
        );
    }

    header('Location: ./?' . ($_backPath ? $_backPath : 's=community_forum&i=' . $communityForumsRow['forum_id']));

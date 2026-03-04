<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');

    $_back = decodeUrlPath(get('b'));
    $_id = get('i');

    $communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_id);
    if (!$communityRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    if (!$GLOBALS['auth']['community']['thread_sticky'])
    {
        makeCookie('notice', 'You do not have access to un/sticky a thread in this forum.');
        header('Location: ./?s=community_thread&i=' . $communityRow['thread_id']);
        die;
    }

    $sticky = false;
    if (get('stick'))
    {
        $sticky = 1;
    }
    elseif (get('unstick'))
    {
        $sticky = 0;
    }
    if ($sticky !== false)
    {
        (new \Databases\CommunityThreads())->setSticky($communityRow['thread_id'], $sticky);
    }

    header('Location: ./?' . ($_back ? $_back : 's=community_thread&i=' . $communityRow['thread_id']));

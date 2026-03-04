<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');

    $_back = decodeUrlPath(get('b'));
    $_id = getInt('i');

    $communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_id);
    if (!$communityRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityBanned($communityRow['community_id']);
    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    if (!$GLOBALS['auth']['community']['thread_lock'])
    {
        makeCookie('notice', 'You do not have access to un/lock a thread in this forum.');
        header('Location: ./?s=community_thread&i=' . $communityRow['thread_id']);
        die;
    }

    $locked = false;
    if (get('lock'))
    {
        $locked = 1;
    }
    elseif (get('unlock'))
    {
        $locked = 0;
    }
    if ($locked !== false)
    {
        (new \Databases\CommunityThreads())->setLocked($communityRow['thread_id'], $locked);
    }

    header('Location: ./?' . ($_back ? $_back : 's=community_thread&i=' . $communityRow['thread_id']));

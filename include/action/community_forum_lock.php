<?php
    require_once('../include/functions/community_permissions.php');

    $_id = getInt('i');

    $communityRow = (new \Databases\CommunityForums())->findActiveForumContext($_id);
    if (!$communityRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

    if (!$GLOBALS['auth']['community']['forum_lock'])
    {
        makeCookie('notice', 'You do not have access to un/lock this forum.');
        header('Location: ./?s=community_forum&i=' . $communityRow['forum_id']);
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
        (new \Databases\CommunityForums())->setLocked($communityRow['forum_id'], $locked);
    }

    header('Location: ./?s=community_forum&i=' . $communityRow['forum_id']);
    die;

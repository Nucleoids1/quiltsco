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

    if (!$GLOBALS['auth']['community']['forum_automate'])
    {
        makeCookie('notice', 'You do not have access to un/automate this forum.');
        header('Location: ./?s=community_thread&i=' . $communityRow['thread_id']);
        die;
    }

    $automate = false;
    if (get('automate'))
    {
        $automate = 1;
    }
    elseif (get('unautomate'))
    {
        $automate = 0;
    }
    if ($automate !== false)
    {
        (new \Databases\CommunityForums())->setAutomated($communityRow['forum_id'], $automate);
    }

    header('Location: ./?s=community_forum&i=' . $communityRow['forum_id']);
    die;

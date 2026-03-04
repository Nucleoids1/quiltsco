<?php
    require_once('../include/functions/community_permissions.php');

    $_id = getInt('i');

    $communityRow = (new \Databases\CommunityForums())->findActiveForumContextInActiveSection($_id);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, you don\'t have permission to modify this community.');
        header('Location: ./?s=community_create');
        die;
    }

    communityPermissions($communityRow['community_id']);

    if (!$GLOBALS['auth']['community']['administration'])
    {
        makeCookie('notice', 'Sorry, you don\'t have permission to modify this community.');
        header('Location: ./?s=community_create');
        die;
    }

    $communityForumsRow = (new \Databases\CommunityForums())->findNextActiveForumByOrder($communityRow['section_id'], $communityRow['forum_order_id']);
    if ($communityForumsRow)
    {
        (new \Databases\CommunityForums())->swapOrderIds($communityRow['forum_id'], $communityRow['forum_order_id'], $communityForumsRow['forum_id'], $communityForumsRow['forum_order_id']);
    }

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;

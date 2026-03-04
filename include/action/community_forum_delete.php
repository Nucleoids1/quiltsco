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

    (new \Databases\CommunityForums())->markDeleted($communityRow['forum_id']);

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);

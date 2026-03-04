<?php
    require_once('../include/functions/community_forum_valid.php');
    require_once('../include/functions/community_permissions.php');

    $_desc = post('description');
    $_id = getInt('i');
    $_name = post('name');

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

    if ($_err = communityForumValid($_name, $_desc))
    {
        makeCookie('notice', $_err);
        header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
    }

    (new \Databases\CommunityForums())->updateDetails($communityRow['forum_id'], $_name, $_name, $_desc, $_desc);

    header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
    die;

<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = post('admin_name');
    $_communityId = getInt('i');

    if (language() == 'french')
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
    }
    else
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
    }

    $communityRow = (new \Databases\Community())->findById($_communityId);
    if (!$communityRow)
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY);
        header('Location: ./?s=community_create');
        die;
    }

    communityPermissions($communityRow['community_id']);

    if (!$GLOBALS['auth']['community']['administration'])
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION);
        header('Location: ./?s=community_create');
        die;
    }

    if (!$userId = getUserId($_adminName))
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    (new \Databases\CommunityPermissions())->grantPermission($communityRow['community_id'], $userId, 'admin');

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

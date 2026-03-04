<?php
    require_once('../include/functions/community_permissions.php');

    $_communityId = getInt('i');
    $_userName = get('name');

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

    if (!$GLOBALS['auth']['community']['administration_ban_user'])
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    if (!$userId = getUserId($_userName))
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    (new \Databases\CommunityBannedUsers())->unbanUser($communityRow['community_id'], $userId);

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

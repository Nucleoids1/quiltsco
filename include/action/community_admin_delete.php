<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = get('admin_name');
    $_communityId = getInt('i');

    if (language() == 'french')
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you do not have permission to modify this community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR', 'Sorry, that user is not a moderator of your community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF', 'Sorry, but you can not edit your own permissions.');
    }
    else
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you do not have permission to modify this community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR', 'Sorry, that user is not a moderator of your community.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF', 'Sorry, but you can not edit your own permissions.');
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

    if ($userId == $GLOBALS['auth']['id'])
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    $communityPermissionsRow = (new \Databases\CommunityPermissions())->findByCommunityAndUser($communityRow['community_id'], $userId);
    if (!$communityPermissionsRow)
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    (new \Databases\CommunityPermissions())->deleteByUserAndCommunity($communityRow['community_id'], $userId);

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

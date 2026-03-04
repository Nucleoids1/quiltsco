<?php
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/ip_encode.php');

    $_id = getInt('i');
    $_ip = encodeIp(get('ip'));

    if (language() == 'french')
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
    }
    else
    {
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
        define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
    }

    $communityRow = (new \Databases\Community())->findById($_id);
    if (!$communityRow)
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY);
        header('Location: ./?s=community_create');
        die;
    }

    communityPermissions($communityRow['community_id']);

    if (!$GLOBALS['auth']['community']['administration_ban_ip'])
    {
        makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    (new \Databases\CommunityBannedIps())->unbanIp($communityRow['community_id'], $_ip);

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

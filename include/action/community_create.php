<?php
    require_once('../include/functions/community_name_valid.php');

    $_communityName = post('name');

    if (language() == 'french')
    {
        define('ACTION_COMMUNITY_CREATE_ROOTADMIN', 'Sorry, you can only be the root admin of five communities.');
    }
    else
    {
        define('ACTION_COMMUNITY_CREATE_ROOTADMIN', 'Sorry, you can only be the root admin of five communities.');
    }

    $communityPermissionsCount = (new \Databases\CommunityPermissions())->countAdministratedCommunitiesByUser($GLOBALS['auth']['id']);
    if ($communityPermissionsCount >= 5)
    {
        makeCookie('notice', ACTION_COMMUNITY_CREATE_ROOTADMIN);
        header('Location: ./?s=community_create');
        die;
    }

    if ($error = communityNameValid($_communityName))
    {
        makeCookie('notice', $error);
        header('Location: ./?s=community_create');
        die;
    }

    $mysqlInsertId = (new \Databases\Community())->createCommunity($_communityName, $GLOBALS['auth']['id']);
    (new \Databases\CommunityPermissions())->grantPermission($mysqlInsertId, $GLOBALS['auth']['id'], 'administrator');

    header('Location: ./?s=community_modify&i=' . $mysqlInsertId);

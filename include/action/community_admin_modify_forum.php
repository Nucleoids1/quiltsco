<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = get('admin_name');
    $_forumId = getInt('i');

    $communityRow = (new \Databases\CommunityForums())->findActiveForumContextInActiveSection($_forumId);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, this forum does not exist.');
        header('Location: ./?s=community_create');
        die;
    }

    communityPermissions($communityRow['community_id']);

    if (!$GLOBALS['auth']['community']['administration'])
    {
        makeCookie('notice', 'Sorry, you don\'t have permission to modify this forum.');
        header('Location: ./?s=community_create');
        die;
    }

    if (!$userId = getUserId($_adminName))
    {
        makeCookie('notice', 'That user does not exist.');
        header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
        die;
    }

    if ($userId == $GLOBALS['auth']['id'])
    {
        makeCookie('notice', 'Sorry, but you can not edit your own permissions.');
        header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
        die;
    }

    $communityForumsPermissionsRow = (new \Databases\CommunityForumsPermissions())->findByForumAndUser($communityRow['forum_id'], $userId);
    if (!$communityForumsPermissionsRow)
    {
        makeCookie('notice', 'Sorry, that user is not an moderator of your forum.');
        header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
        die;
    }

    (new \Databases\CommunityForumsPermissions())->deleteByForumAndUser($communityRow['forum_id'], $userId);

    $communityForumsPermissionsRows = (new \Databases\CommunityForumsPermissions())->selectDistinctPermissionsExcept('admin');
    foreach ($communityForumsPermissionsRows AS $communityForumsPermissionsRow)
    {
        if (post($communityForumsPermissionsRow['permission']))
        {
            (new \Databases\CommunityForumsPermissions())->grantPermission($communityRow['forum_id'], $userId, $communityForumsPermissionsRow['permission']);
        }
    }

    header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
    die;

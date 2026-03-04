<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = get('admin_name');
    $_sectionId = getInt('i');

    $communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_sectionId);
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
        header('Location: ./?s=community_forum_modify&i=' . $communityRow['section_id']);
        die;
    }

    if ($userId == $GLOBALS['auth']['id'])
    {
        makeCookie('notice', 'Sorry, but you can not edit your own permissions.');
        header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
        die;
    }

    $communitySectionsPermissionsRow = (new \Databases\CommunitySectionsPermissions())->findBySectionAndUser($communityRow['section_id'], $userId);
    if (!$communitySectionsPermissionsRow)
    {
        makeCookie('notice', 'Sorry, that user is not an moderator of your forum.');
        header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
        die;
    }

    (new \Databases\CommunitySectionsPermissions())->deleteBySectionAndUser($communityRow['section_id'], $userId);

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;

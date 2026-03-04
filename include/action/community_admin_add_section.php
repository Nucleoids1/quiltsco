<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = post('admin_name');
    $_sectionId = getInt('i');

    $communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_sectionId);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, this section does not exist.');
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
        header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
        die;
    }

    if ($userId == $GLOBALS['auth']['id'])
    {
        makeCookie('notice', 'Sorry, but you can not edit your own permissions.');
        header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
        die;
    }

    (new \Databases\CommunitySectionsPermissions())->grantPermissionOrUpdate($communityRow['section_id'], $userId, 'admin');

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;

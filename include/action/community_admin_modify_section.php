<?php
    require_once('../include/functions/community_permissions.php');

    $_adminName = get('admin_name');
    $_sectionId = getInt('i');

    $communitySectionsRow = (new \Databases\CommunitySections())->findById($_sectionId);
    if (!$communitySectionsRow || $communitySectionsRow['section_deleted'])
    {
        makeCookie('notice', 'Sorry, this forum does not exist.');
        header('Location: ./?s=community_create');
        die;
    }

    $communityRow = (new \Databases\Community())->findById($communitySectionsRow['community_id']);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, this forum does not exist.');
        header('Location: ./?s=community_create');
        die;
    }

    $communityRowMerged = array_merge($communitySectionsRow, $communityRow);

    communityPermissions($communityRowMerged['community_id']);

    if (!$GLOBALS['auth']['community']['administration'])
    {
        makeCookie('notice', 'Sorry, you don\'t have permission to modify this forum.');
        header('Location: ./?s=community_create');
        die;
    }

    if (!$userId = getUserId($_adminName))
    {
        makeCookie('notice', 'That user does not exist.');
        header('Location: ./?s=community_section_modify&i=' . $communityRowMerged['section_id']);
        die;
    }

    if ($userId == $GLOBALS['auth']['id'])
    {
        makeCookie('notice', 'Sorry, but you can not edit your own permissions.');
        header('Location: ./?s=community_section_modify&i=' . $communityRowMerged['section_id']);
        die;
    }

    if (!(new \Databases\CommunitySectionsPermissions())->existsBySectionAndUser($communityRowMerged['section_id'], $userId))
    {
        makeCookie('notice', 'Sorry, that user is not a section moderator.');
        header('Location: ./?s=community_section_modify&i=' . $communityRowMerged['section_id']);
        die;
    }

    (new \Databases\CommunitySectionsPermissions())->deleteBySectionAndUser($communityRowMerged['section_id'], $userId);
    (new \Databases\CommunitySectionsPermissions())->grantPermission($communityRowMerged['section_id'], $userId, 'admin');

    $communitySectionsPermissionsRows = (new \Databases\CommunitySectionsPermissions())->selectDistinctPermissionsExcept('admin');
    foreach ($communitySectionsPermissionsRows as $communitySectionsPermissionsRow)
    {
        $communitySectionsPermission = $communitySectionsPermissionsRow['permission'];
        if (post($communitySectionsPermission))
        {
            (new \Databases\CommunitySectionsPermissions())->grantPermission($communityRowMerged['section_id'], $userId, $communitySectionsPermission);
        }
    }

    header('Location: ./?s=community_section_modify&i=' . $communityRowMerged['section_id']);
    die;

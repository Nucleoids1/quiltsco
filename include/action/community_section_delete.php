<?php
    require_once('../include/functions/community_permissions.php');

    $_id = getInt('i');

    $communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_id);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, but what you are trying to access does not exist.');
        header('Location: ./?s=community_create');
        die;
    }

    communityPermissions($communityRow['community_id']);

    if (!$GLOBALS['auth']['community']['administration'])
    {
        makeCookie('notice', 'Sorry, you do not have permission to modify this community.');
        header('Location: ./?s=community_create');
        die;
    }

    (new \Databases\CommunitySections())->markDeleted($communityRow['section_id']);
    (new \Databases\CommunityForums())->markDeletedBySection($communityRow['section_id']);

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

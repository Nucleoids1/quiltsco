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

    $communitySectionsRow = (new \Databases\CommunitySections())->findNextActiveSectionByOrder($communityRow['community_id'], $communityRow['section_order_id']);
    if ($communitySectionsRow)
    {
        (new \Databases\CommunitySections())->updateOrderId($communityRow['section_id'], $communitySectionsRow['section_order_id']);
        (new \Databases\CommunitySections())->updateOrderId($communitySectionsRow['section_id'], $communityRow['section_order_id']);
    }

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

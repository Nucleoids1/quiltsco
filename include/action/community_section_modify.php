<?php
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_section_valid.php');

    $_id = getInt('i');
    $_name = post('name');

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
        makeCookie('notice', 'Sorry, you don\'t have permission to modify this community.');
        header('Location: ./?s=community_create');
        die;
    }

    if ($_err = communitySectionValid($_name))
    {
        makeCookie('notice', $_err);
        header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
        die;
    }

    (new \Databases\CommunitySections())->updateName($communityRow['section_id'], $_name, $_name);

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;

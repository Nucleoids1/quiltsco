<?php
    require_once('../include/functions/community_forum_valid.php');
    require_once('../include/functions/community_permissions.php');

    $_forumDescription = post('description');
    $_forumName = post('name');
    $_sectionId = getInt('i');

    $communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_sectionId);
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

    if ($error = communityForumValid($_forumName, $_forumDescription))
    {
        makeCookie('notice', $error);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    $communityForumsRow = (new \Databases\CommunityForums())->findMaxOrderBySection($communityRow['section_id']);
    if ($communityForumsRow)
    {
        $orderId = $communityForumsRow['forum_order_id'] + 1;
    }
    else
    {
        $orderId = 0;
    }

    (new \Databases\CommunityForums())->createForum(
        $communityRow['section_id'],
        $_forumName,
        $_forumName,
        $_forumDescription,
        $_forumDescription,
        $orderId
    );

    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);

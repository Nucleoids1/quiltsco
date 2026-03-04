<?php
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_section_valid.php');

    $_forumName = post('name');
    $_sectionId = getInt('i');

    $communityRow = (new \Databases\Community())->findById($_sectionId);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, but this community does not exist.');
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

    if ($error = communitySectionValid($_forumName))
    {
        makeCookie('notice', $error);
        header('Location: ./?s=community_modify&i=' . $communityRow['id']);
        die;
    }

    $communitySectionsRow = (new \Databases\CommunitySections())->findMaxOrderByCommunity($communityRow['community_id']);
    if ($communitySectionsRow)
    {
        $orderId = $communitySectionsRow['section_order_id'] + 1;
    }
    else
    {
        $orderId = 0;
    }

    $mysqlInsertId = (new \Databases\CommunitySections())->createSection(
        $communityRow['community_id'],
        $_forumName,
        $_forumName,
        $orderId
    );
    (new \Databases\CommunityForums())->createForum(
        $mysqlInsertId,
        'Untitled Forum',
        'Untitled Forum',
        'The quick brown fox jumps over the lazy dog.',
        'The quick brown fox jumps over the lazy dog.',
        0
    );

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

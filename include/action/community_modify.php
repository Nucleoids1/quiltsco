<?php
    require_once('../include/functions/community_name_valid.php');
    require_once('../include/functions/community_permissions.php');

    $_id = getInt('i');
    $_name = post('name');

    $communityRow = (new \Databases\Community())->findById($_id);
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

    if ($error = communityNameValid($_name))
    {
        makeCookie('notice', $error);
        header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
        die;
    }

    (new \Databases\Community())->updateName($communityRow['community_id'], $_name);

    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;

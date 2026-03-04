<?php
    $_id = getInt('i');
    $_user = get('u');

    if (!(new \Databases\QuiltsPermissions())->hasRootPermissionByUserAndQuilt($GLOBALS['auth']['id'], $_id))
    {
        header('Location: ./?s=quilts_moderate');
        die;
    }

    $membersRow = (new \Databases\Members())->findByUsernameExcludingUserId($_user, $GLOBALS['auth']['id']);
    if ($membersRow)
    {
        (new \Databases\QuiltsPermissions())->deleteByUserAndQuilt($membersRow['id'], $_id);
    }

    header('Location: ./?s=quilt_edit&i=' . $_id);
    die;

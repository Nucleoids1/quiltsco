<?php
    $_id = getInt('i');
    $_name = post('name');

    if (!(new \Databases\QuiltsPermissions())->hasRootPermissionByUserAndQuilt($GLOBALS['auth']['id'], $_id))
    {
        header('Location: ./?s=quilts_moderate');
        die;
    }

    $membersRow = (new \Databases\Members())->findByUsernameExcludingUserId($_name, $GLOBALS['auth']['id']);
    if ($membersRow)
    {
        if (!(new \Databases\QuiltsPermissions())->hasPermissionByUserAndQuilt($membersRow['id'], $_id))
        {
            (new \Databases\QuiltsPermissions())->grantPermission($membersRow['id'], $_id, 'moderator');
        }
    }

    header('Location: ./?s=quilt_edit&i=' . $_id);
    die;

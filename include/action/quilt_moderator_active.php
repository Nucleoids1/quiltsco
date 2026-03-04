<?php
    $_id = getInt('i');

    $quiltsPermissionsRow = (new \Databases\QuiltsPermissions())->findByUserAndQuilt($GLOBALS['auth']['id'], $_id);
    if ($quiltsPermissionsRow)
    {
        if ($quiltsPermissionsRow['active'])
        {
            (new \Databases\QuiltsPermissions())->deactivate($GLOBALS['auth']['id'], $_id);
        }
        else
        {
            (new \Databases\QuiltsPermissions())->activate($GLOBALS['auth']['id'], $_id);
        }
    }

    header('Location: ./?s=quilts_moderate');
    die;

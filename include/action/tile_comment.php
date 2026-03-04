<?php
    $_back = decodeUrlPath(get('b'), 's=finished');
    $_comment = post('comment');
    $_id = getInt('i');

    if ($_id)
    {
        $tilesRow = (new \Databases\Tiles())->findById($_id);
        if ($tilesRow)
        {
            $hasQuiltPermission = (new \Databases\QuiltsPermissions())->hasPermissionByUserAndQuilt($GLOBALS['auth']['id'], $tilesRow['quilt_id']);
            $isTileOwner = $tilesRow['user_id'] == $GLOBALS['auth']['id'];
            $isPrivilegedRole = !empty($GLOBALS['auth']['root']);

            if ($hasQuiltPermission || $isTileOwner || $isPrivilegedRole)
            {
                (new \Databases\Tiles())->updateComment($_id, $_comment);
            }
            else
            {
                makeCookie('notice', 'Sorry, you do not have permission to update this tile comment.');
            }
        }
    }

    header('Location: ./?' . $_back);
    die;

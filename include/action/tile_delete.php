<?php
    require_once('../include/functions/create_quilt_image_cache.php');

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
                (new \Databases\Tiles())->softDelete($_id);
                createQuiltImageCache($tilesRow['quilt_id']);
            }
            else
            {
                makeCookie('notice', 'Sorry, you do not have permission to delete this tile.');
            }
        }
    }

    header('Location: ./?s=tile&i=' . $_id);
    die;

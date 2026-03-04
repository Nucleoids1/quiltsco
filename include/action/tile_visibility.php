<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_id = getInt('i');
    $_visibility = postInt('visibility', 0, true);

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
                if (in_array($_visibility, array(-1, 0, 1), true))
                {
                    (new \Databases\Tiles())->setVisibility($_id, $_visibility);
                    createQuiltImageCache($tilesRow['quilt_id']);
                }
                else
                {
                    makeCookie('notice', 'Something went wrong!');
                }
            }
            else
            {
                makeCookie('notice', 'Sorry, you do not have permission to change tile visibility.');
            }
        }
    }

    header('Location: ./?s=tile&i=' . $_id);
    die;

<?php
    $_id = getInt('i');

    if ((new \Databases\QuiltsPermissions())->hasRootPermissionByUserAndQuilt($GLOBALS['auth']['id'], $_id))
    {
         $quiltsRow = (new \Databases\Quilts())->findById($_id);
        if ($quiltsRow)
        {
            $tilesPendingCount = (new \Databases\TilesPending())->countByQuilt($quiltsRow['id']);
            if (!$tilesPendingCount)
            {
                $number = $quiltsRow['quilt_width'] * $quiltsRow['quilt_height'];
                 $tilesCount = (new \Databases\Tiles())->countByQuiltNotDeleted($quiltsRow['id']);
                if ($number == $tilesCount)
                {
                     (new \Databases\Quilts())->markFinished($quiltsRow['id']);
                }
            }
        }
    }

    header('Location: ./?s=quilts_moderate');
    die;

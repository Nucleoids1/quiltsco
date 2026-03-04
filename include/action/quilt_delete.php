<?php
    $_id = getInt('i');

    if ((new \Databases\QuiltsPermissions())->hasPermissionByUserAndQuilt($GLOBALS['auth']['id'], $_id))
    {
         $quiltsRow = (new \Databases\Quilts())->findById($_id);
        if ($quiltsRow)
        {
             $tilesCount = (new \Databases\Tiles())->countByQuilt($quiltsRow['id']);
            $tilesPendingCount = (new \Databases\TilesPending())->countByQuilt($quiltsRow['id']);
            if (!$tilesCount && !$tilesPendingCount)
            {
                 (new \Databases\Quilts())->deleteById($quiltsRow['id']);
                 (new \Databases\Comments('quilts_comments'))->deleteByLinkId($quiltsRow['id']);
                (new \Databases\QuiltsPermissions())->deleteByUserAndQuilt($GLOBALS['auth']['id'], $quiltsRow['id']);
            }
        }
    }

    header('Location: ./?s=quilts_moderate');
    die;

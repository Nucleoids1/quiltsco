<?php
    $_id = getInt('i');
    $_reason = post('reason');
    $_todo = post('todo');

    $tilesRow = (new \Databases\Tiles())->findPendingById($_id);
    if ($tilesRow)
    {
        $quiltsPermissionsRow = (new \Databases\QuiltsPermissions())->findByUserAndQuilt($GLOBALS['auth']['id'], $tilesRow['quilt_id']);
        if ($quiltsPermissionsRow && ($_todo == 0 || $_todo == 1))
        {
            if ($_todo == 0)
            {
                (new \Databases\Tiles())->rejectTile($_id);
            }
            elseif ($_todo == 1)
            {
                (new \Databases\Tiles())->approveTile($_id);
            }
            if ($_reason)
            {
                if ($GLOBALS['auth']['id'] != $tilesRow['user_id'])
                {
                    (new \Databases\Messages())->sendMessage(
                        $GLOBALS['auth']['id'],
                        $tilesRow['user_id'],
                        '[' . ($_todo == 0 ? 'Rejected' : 'Approved') . '] About Tile #' . $tilesRow['tile_id'] . ' With Comment: ' . ($tilesRow['comment'] ? $tilesRow['comment'] : 'No Comment') . "\r\n\r\b" . $_reason
                    );
                }
            }
        }
    }

    header('Location: ./?s=approvals');
    die;

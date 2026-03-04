<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_comment = post('comment');
    $_id = getInt('i');
    $_redirectPath = $_back ? './?' . $_back : './?s=quilt&i=' . $_id;
    $_x = getInt('x');
    $_y = getInt('y');

    $tilesPendingRow = (new \Databases\TilesPending())->findByQuiltAndMatrixAndUser($_id, $_x, $_y, $GLOBALS['auth']['id']);
    if ($tilesPendingRow)
    {
        $quiltsRow = (new \Databases\Quilts())->findById($_id);
        if ($quiltsRow)
        {
            $_tile = files('tile');
            if (!$_tile)
            {
                redirectWithNotice('No File', $_redirectPath);
            }

            if (!isset($_tile['type']) || $_tile['type'] == '')
            {
                redirectWithNotice('Invalid File Type', $_redirectPath);
            }

            if ($_tile['type'] != 'image/png')
            {
                redirectWithNotice('Invalid File Type', $_redirectPath);
            }

            if (!isset($_tile['tmp_name']) || !$_tile['tmp_name'])
            {
                redirectWithNotice('Corrupt Image', $_redirectPath);
            }

            $handle = @imagecreatefrompng($_tile['tmp_name']);
            if (!$handle)
            {
                redirectWithNotice('Corrupt Image', $_redirectPath);
            }

            $srcWidth = imagesx($handle);
            $srcHeight = imagesy($handle);
            if (!$srcWidth || !$srcHeight)
            {
                redirectWithNotice('Corrupt Image', $_redirectPath);
            }

            if ($srcWidth != $quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2)
            {
                redirectWithNotice('The width of the tile you are trying to upload is the wrong size.', $_redirectPath);
            }

            if ($srcHeight != $quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2)
            {
                redirectWithNotice('The height of the tile you are trying to upload is the wrong size.', $_redirectPath);
            }

            $newHandle = imagecreatetruecolor($quiltsRow['tile_width'], $quiltsRow['tile_height']);
            imagecopy($newHandle, $handle, 0, 0, $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], $quiltsRow['tile_width'], $quiltsRow['tile_height']);
            if (1)
            {
                $temp = makeCacheCode();
                imagepng($newHandle, '../temp/' . $temp);
                $imgData = file_get_contents('../temp/' . $temp);
                unlink('../temp/' . $temp);
            }
            else
            {
                ob_start();
                imagepng($newHandle);
                $imgData = ob_get_contents();
                ob_end_clean();
            }
            list($date, $hours) = explode(' ', $tilesPendingRow['started_on']);
            list($year, $month, $day) = explode('-', $date);
            list($hour, $min, $sec) = explode(':', $hours);
            $dateStart = mktime($hour, $min, $sec, $month, $day, $year);
            $submitDate = date('Y-m-d H:i:s', server('REQUEST_TIME'));
            list($date, $hours) = explode(' ', $submitDate);
            list($year, $month, $day) = explode('-', $date);
            list($hour, $min, $sec) = explode(':', $hours);
            $dateEnd = mktime($hour, $min, $sec, $month, $day, $year);
            $seconds = $dateEnd - $dateStart;
            (new \Databases\Tiles())->submitTile(
                $_id,
                $_x,
                $_y,
                $GLOBALS['auth']['id'],
                $_comment,
                $tilesPendingRow['started_on'],
                $seconds,
                $tilesPendingRow['borders'],
                $quiltsRow['moderated'] ? -1 : 1,
                $imgData
            );
            (new \Databases\TilesPending())->deleteByQuiltAndMatrixAndUserId($_id, $_x, $_y, $GLOBALS['auth']['id']);
            (new \Databases\Quilts())->touch($_id);
            createQuiltImageCache($_id);
            makeCookie('notice', 'Tile Uploaded');
        }
    }
    header('Location: ' . $_redirectPath);
    die;

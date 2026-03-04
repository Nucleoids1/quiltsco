<?php
    $_borders = get('borders', 'TLTCTRMLMCMRBLBCBR');
    $_id = getInt('i');
    $_x = getInt('x');
    $_y = getInt('y');

    if ($_id && $_x && $_y)
    {
        $quiltsRow = (new \Databases\Quilts())->findById($_id);
        if ($quiltsRow)
        {
            $xMinus = $_x - 1;
            $xPlus = $_x + 1;
            $yMinus = $_y - 1;
            $yPlus = $_y + 1;
            if ($quiltsRow['edge_wrap'] == 1 || $quiltsRow['edge_wrap'] == 3)
            {
                if ($xMinus < 1)
                {
                    $xMinus = $quiltsRow['quilt_width'];
                }
                if ($xPlus > $quiltsRow['quilt_width'])
                {
                    $xPlus = 1;
                }
            }
            if ($quiltsRow['edge_wrap'] == 2 || $quiltsRow['edge_wrap'] == 3)
            {
                if ($yMinus < 1)
                {
                    $yMinus = $quiltsRow['quilt_height'];
                }
                if ($yPlus > $quiltsRow['quilt_height'])
                {
                    $yPlus = 1;
                }
            }
            $imDest = imagecreatetruecolor($quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2, $quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2);
            $grey1 = imagecolorallocate($imDest, 128, 128, 128);
            $grey2 = imagecolorallocate($imDest, 64, 64, 64);
            $xcols = $quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2;
            $ycols = $quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2;
            $x = 0;
            while ($x < $xcols / 8)
            {
                $y = 0;
                while ($y < $ycols / 8)
                {
                    imagefilledrectangle($imDest, $x * 8, $y * 8, $x * 8 + 8, $y * 8 + 8, (($y + $x) % 2 == 0 ? $grey1 : $grey2));
                    $y++;
                }
                $x++;
            }
            $white = imagecolorallocate($imDest, 255, 255, 255);
            imagefilledrectangle($imDest, $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], $quiltsRow['tile_width'] + $quiltsRow['side_pixels'] - 1, $quiltsRow['tile_height'] + $quiltsRow['side_pixels'] - 1, $white);

            copyTile($_borders, 'TL', $imDest, $quiltsRow, $xMinus, $yMinus, 0, 0, $quiltsRow['tile_width'] - $quiltsRow['side_pixels'], $quiltsRow['tile_height'] - $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], $quiltsRow['side_pixels']);
            copyTile($_borders, 'TC', $imDest, $quiltsRow, $_x, $yMinus, $quiltsRow['side_pixels'], 0, 0, $quiltsRow['tile_height'] - $quiltsRow['side_pixels'], $quiltsRow['tile_width'], $quiltsRow['side_pixels']);
            copyTile($_borders, 'TR', $imDest, $quiltsRow, $xPlus, $yMinus, $quiltsRow['tile_width'] + $quiltsRow['side_pixels'], 0, 0, $quiltsRow['tile_height'] - $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], $quiltsRow['side_pixels']);
            copyTile($_borders, 'ML', $imDest, $quiltsRow, $xMinus, $_y, 0, $quiltsRow['side_pixels'], $quiltsRow['tile_width'] - $quiltsRow['side_pixels'], 0, $quiltsRow['side_pixels'], $quiltsRow['tile_height']);
            copyTile($_borders, 'MC', $imDest, $quiltsRow, $_x, $_y, $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], 0, 0, $quiltsRow['tile_width'], $quiltsRow['tile_height']);
            copyTile($_borders, 'MR', $imDest, $quiltsRow, $xPlus, $_y, $quiltsRow['tile_width'] + $quiltsRow['side_pixels'], $quiltsRow['side_pixels'], 0, 0, $quiltsRow['side_pixels'], $quiltsRow['tile_height']);
            copyTile($_borders, 'BL', $imDest, $quiltsRow, $xMinus, $yPlus, 0, $quiltsRow['tile_height'] + $quiltsRow['side_pixels'], $quiltsRow['tile_width'] - $quiltsRow['side_pixels'], 0, $quiltsRow['side_pixels'], $quiltsRow['side_pixels']);
            copyTile($_borders, 'BC', $imDest, $quiltsRow, $_x, $yPlus, $quiltsRow['side_pixels'], $quiltsRow['tile_height'] + $quiltsRow['side_pixels'], 0, 0, $quiltsRow['tile_width'], $quiltsRow['side_pixels']);
            copyTile($_borders, 'BR', $imDest, $quiltsRow, $xPlus, $yPlus, $quiltsRow['tile_width'] + $quiltsRow['side_pixels'], $quiltsRow['tile_height'] + $quiltsRow['side_pixels'], 0, 0, $quiltsRow['side_pixels'], $quiltsRow['side_pixels']);

            header('content-type: image/png');
            header('content-disposition: inline; filename="' . $quiltsRow['id'] . '_' . $_x . '_' . $_y . '.png"');
            imagepng($imDest);
        }
        else
        {
            displayEmptyImage();
        }
    }
    else
    {
        displayEmptyImage();
    }

    function copyTile($borders, $code, $imDest, $quiltsRow, $matrixX, $matrixY, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH)
    {
        if (strpos($borders, $code) !== false)
        {
            $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($quiltsRow['id'], $matrixX, $matrixY);
            if ($tilesRow)
            {
                $imSrc = imagecreatefromstring($tilesRow['data_tile']);
                imagecopy($imDest, $imSrc, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH);
            }
        }
    }

    function displayEmptyImage()
    {
        $im = imagecreatetruecolor(intval(200 + (SIDE_PIXELS * 2)), intval(200 + (SIDE_PIXELS * 2)));
        imagefill($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, SIDE_PIXELS, SIDE_PIXELS, 200 + SIDE_PIXELS - 1, 200 + SIDE_PIXELS - 1, $white);
        header ("Content-type: image/png");
        header('content-disposition: inline; filename="empty.png"');
        imagepng($im);
    }

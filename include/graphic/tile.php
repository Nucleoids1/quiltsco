<?php
    require_once('../include/functions/tile_is_available.php');

    $_id = getInt('i');

    if ($_id)
    {
        $tilesRow = (new \Databases\Tiles())->findById($_id);
        if ($tilesRow)
        {
            list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y']);
            if ($display)
            {
                header('content-type: image/png');
                header('content-disposition: inline; filename="' . $tilesRow['tile_id'] . '.png"');
                echo $tilesRow['data_tile'];
                (new \Databases\Tiles())->incrementViews($tilesRow['tile_id']);
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
    }
    else
    {
        displayEmptyImage();
    }

    function displayEmptyImage()
    {
        $im = imagecreatetruecolor(264, 264);
        imagefill($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 32, 32, 200 + 32 - 1, 200 + 32 - 1, $white);
        header ("Content-type: image/png");
        header('content-disposition: inline; filename="empty.png"');
        imagepng($im);
    }

<?php
    require_once('../include/functions/image_directory.php');

    $_id = getInt('i');

    if ($_id)
    {
        $binariesDatabase = new \Databases\ImagesBinaries(binariesPath($_id));
        $binariesRow = $binariesDatabase->findByImageIdWithFields($_id, 'image_id, thumb');
        if ($binariesRow)
        {
            header('content-type: image/png');
            header('content-disposition: inline; filename="' . $binariesRow['image_id'] . '.png"');
            echo $binariesRow['thumb'];
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
        imagefilledrectangle($im, SIDE_PIXELS, SIDE_PIXELS, 200 + SIDE_PIXELS - 1, 200 + SIDE_PIXELS - 1, $white);
        header ("Content-type: image/png");
        header('content-disposition: inline; filename="empty.png"');
        imagepng($im);
    }

<?php
    $_id = getInt('i');

    $quiltsRow = (new \Databases\Quilts())->findById($_id);
    if ($quiltsRow)
    {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="quilt_thumb_' . $quiltsRow['id'] . '.png"');
        echo $quiltsRow['data_thumb'];
    }
    else
    {
        displayEmptyImage();
    }

    function displayEmptyImage()
    {
        $im = imagecreatetruecolor(300, 300);
        imagefill($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 50, 50, 300 - 50 - 1, 300 - 50 - 1, $white);
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="empty.png"');
        imagepng($im);
    }

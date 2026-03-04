<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_force = getInt('force');
    $_id = getInt('i');

    $quiltsRow = (new \Databases\Quilts())->findById($_id);
    if ($quiltsRow)
    {
        if ($_force) {
            createQuiltImageCache($_id);
            $quiltsRow = (new \Databases\Quilts())->findById($_id);
        }
        if (get('format') == 'jpg')
        {
            header('Content-Type: image/jpeg');
            header('Content-Disposition: inline; filename="quilt_full_' . $quiltsRow['id'] . '.jpg"');
            echo $quiltsRow['data_full_jpg'];
        }
        else
        {
            header('Content-Type: image/png');
            header('Content-Disposition: inline; filename="quilt_full_' . $quiltsRow['id'] . '.png"');
            echo $quiltsRow['data_full_png'];
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
        if (get('format') == 'jpg')
        {
            header('Content-Type: image/jpeg');
            header('Content-Disposition: inline; filename="empty.jpg"');
            imagejpeg($im, '', 90);
        }
        else
        {
            header('Content-Type: image/png');
            header('Content-Disposition: inline; filename="empty.png"');
            imagepng($im);
        }
    }

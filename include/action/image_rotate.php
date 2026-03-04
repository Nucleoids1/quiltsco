<?php
    require_once('../include/functions/image_directory.php');
    require_once('../include/functions/image_upload.php');

    $_back = decodeUrlPath(get('b'), 's=finished');
    $_degrees = getInt('degrees');
    $_id = getInt('i');

    if ($_degrees != 90 && $_degrees != 180 && $_degrees != 270)
    {
        makeCookie('notice', 'Please stop playing with our scripts!');
        header('Location: ./?s=finished');
        die;
    }

    $_degrees = 360 - $_degrees;

    if ($GLOBALS['auth']['rotate'])
    {
        $imagesRow = (new \Databases\Images())->findById($_id);
        if ($imagesRow)
        {
            $binariesRow = (new \Databases\ImagesBinaries(binariesPath($imagesRow['id'])))->findByImageId($imagesRow['id']);
            if ($binariesRow)
            {
                if ($imagesRow['height'] < 160 && ($_degrees == 90 || $_degrees == 270))
                {
                    makeCookie('notice', 'This image can\'t be rotated.');
                    header('Location: ./?' . $_back);
                    die;
                }
                if (!$convert = @imagecreatefromstring($binariesRow['full']))
                {
                    makeCookie('notice', 'This image can\'t be rotated.');
                    header('Location: ./?' . $_back);
                    die;
                }
                (new \Databases\ImagesBinariesRotate())->saveRotation($binariesRow['image_id'], $binariesRow['full'], $binariesRow['thumb'], $binariesRow['original']);
                $handle = imagecreatefromstring($binariesRow['full']);
                $handle = imagerotate($handle, $_degrees, 0);
                $shrinkSize = shrinkImage($handle, '', $imagesRow['file_type'], IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT, $imagesRow['id'], 1);
                $thumbSize = thumbImage($handle, $imagesRow['file_type'], THUMB_WIDTH, THUMB_HEIGHT, $imagesRow['id']);
                if ($_degrees == 90 || $_degrees == 270)
                {
                    (new \Databases\Images())->swapDimensions($imagesRow['id'], $imagesRow['height'], $imagesRow['width']);
                }
                makeCookie('notice', 'Rotated.');
            }
        }
    }

    header('Location: ./?' . $_back . '&rand=1');
    die;

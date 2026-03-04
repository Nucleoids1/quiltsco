<?php
    require_once('../include/functions/image_directory.php');

    $_id = getInt('i');

    if ($_id)
    {
        $binariesDatabase = new \Databases\ImagesBinaries(binariesPath($_id));
        $binariesRow = $binariesDatabase->findByImageIdWithFields($_id, 'image_id, full');
        if ($binariesRow)
        {
            (new \Databases\Images())->incrementViews($binariesRow['image_id']);
            header('content-type: image/jpeg');
            header('content-disposition: inline; filename="full_' . $binariesRow['image_id'] . '.jpg"');
            echo $binariesRow['full'];
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
        header('content-type: image/jpeg');
        header('content-disposition: inline;');
        $handle = imagecreatefrompng(SITE_PATH.'themes/rave/images/no_image.png');
        imagejpeg($handle , '', 90);
    }

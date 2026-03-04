<?php
    $_id = getInt('i');
    $_page = getInt('p', 1);

    if ($GLOBALS['auth']['id'])
    {
        $galleryImagesRow = (new \Databases\GalleryImages())->findOneByImageIdAndUserId($_id, $GLOBALS['auth']['id']);
        if ($galleryImagesRow && $galleryImagesRow['user_id'] == $GLOBALS['auth']['id'])
        {
            (new \Databases\Members())->setMainImage($GLOBALS['auth']['id'], $_id);
        }
    }

    header('Location: ./?s=upload_image&p=' . $_page);
    die;

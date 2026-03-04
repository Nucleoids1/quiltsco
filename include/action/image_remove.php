<?php
    $_id = getInt('i');
    $_page = getInt('p', 1);

    if ($GLOBALS['auth']['id'])
    {
        (new \Databases\GalleryImages())->deleteByImageIdAndUserId($_id, $GLOBALS['auth']['id']);
    }

    header('Location: ./?s=upload_image&p=' . $_page);
    die;

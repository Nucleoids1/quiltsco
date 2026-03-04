<?php
    $_page = getInt('p', 1);

    if ($GLOBALS['auth']['id'])
    {
        (new \Databases\Members())->setMainImage($GLOBALS['auth']['id'], 0);
    }

    header('Location: ./?s=upload_image&p=' . $_page);
    die;

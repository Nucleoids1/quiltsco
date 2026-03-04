<?php
    $_id = getInt('i');

    if ($GLOBALS['auth']['root'])
    {
        (new \Databases\Images())->deleteById($_id);
        (new \Databases\GalleryImages())->deleteByImageId($_id);
    }

    header('Location: ./?s=graphix');
    die;

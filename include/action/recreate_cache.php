<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_id = get('i');

    if ($GLOBALS['auth']['root'])
    {
        $quiltsRows = (new \Databases\Quilts())->selectAllOrderedById($_id ? (int)$_id : null);
        foreach ($quiltsRows as $quiltsRow)
        {
            createQuiltImageCache($quiltsRow['id']);
            echo 'Created Image Cache For ' . $quiltsRow['name'] . '<br />';
        }
    }

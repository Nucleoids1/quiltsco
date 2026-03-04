<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_description = post('description');
    $_level = postInt('level');
    $_moderated = postInt('moderated');
    $_multiple = postInt('multiple', 1);
    $_name = post('name');
    $_quiltHeight = postInt('quilt_height');
    $_quiltWidth = postInt('quilt_width');
    $_showAll = postInt('show_all');
    $_sidePixels = postInt('side_pixels');
    $_tileHeight = postInt('tile_height');
    $_tileWidth = postInt('tile_width');
    $_timeLimit = postInt('timelimit');
    $_workOnAll = postInt('work_on_all');

    if ($_level > 2 || $_level < 0)
    {
        $_level = 0;
    }

    $errors = '';

    if (strlen($_name) < 4)
    {
        $errors .= 'The name of the quilt must be 4 or more characters.<br />';
    }

    if ($_quiltWidth < 1)
    {
        $errors .= 'The width of the quilt must be greater then or equal to 1.<br />';
    }
    elseif ($_quiltWidth > 15)
    {
        $errors .= 'The width of the quilt must be less then or equal to 15.<br />';
    }

    if ($_quiltHeight < 1)
    {
        $errors .= 'The height of the quilt must be greater then or equal to 1.<br />';
    }
    elseif ($_quiltHeight > 15)
    {
        $errors .= 'The height of the quilt must be less then or equal to 15.<br />';
    }

    if ($_tileWidth < 100)
    {
        $errors .= 'The width of the tile must be greater then or equal to 100.<br />';
    }
    elseif ($_tileWidth > 800)
    {
        $errors .= 'The width of the tile must be less then or equal to 256.<br />';
    }

    if ($_tileHeight < 100)
    {
        $errors .= 'The height of the tile must be greater then or equal to 100.<br />';
    }
    elseif ($_tileHeight > 800)
    {
        $errors .= 'The height of the tile must be less then or equal to 256.<br />';
    }

    if ($_quiltWidth * $_tileWidth > 1600)
    {
        $errors .= 'The total width of a quilt can not be more then 1600 pixels at this time.<br />';
    }

    if ($_quiltHeight * $_tileHeight > 5000)
    {
        $errors .= 'The total height of a quilt can not be more then 5000 pixels at this time.<br />';
    }

    if ($_timeLimit < 1)
    {
        $errors .= 'The minimum amount of time to work on a tile must be greater or equal to 1 hour.<br />';
    }
    elseif ($_timeLimit > 72)
    {
        $errors .= 'The maximum amount of time to work on a tile must be less than or equal to 72 hours.<br />';
    }

    if ($_multiple < 1)
    {
        $errors .= 'The minimum amount of tiles one can work on must be greater or equal to 1.<br />';
    }
    elseif ($_multiple > 10)
    {
        $errors .= 'The maximum amount of tiles one can work on must be less than or equal to 10.<br />';
    }

    if ($_sidePixels != 15 && $_sidePixels != 16 && $_sidePixels != 20 && $_sidePixels != 24 && $_sidePixels != 25 && $_sidePixels != 30 && $_sidePixels != 32 && $_sidePixels != 35 && $_sidePixels != 40 && $_sidePixels != 45 && $_sidePixels != 50)
    {
        $errors .= 'The side pixels people can work with must be either 15, 16, 20, 24, 25, 30, 32, 35, 40, 45 or 50.<br />';
    }

    if ($errors)
    {
        include('../include/show/quilt_create.php');
        die;
    }
    else
    {
        $timelimit = $_timeLimit * 60 * 60;
        $mysqlInsertId = (new \Databases\Quilts())->createQuilt(
            $_name,
            $_description,
            $_quiltWidth,
            $_quiltHeight,
            $_tileWidth,
            $_tileHeight,
            $timelimit,
            $_sidePixels,
            $_level,
            $_showAll,
            $_workOnAll,
            $_multiple,
            $_moderated
        );
        createQuiltImageCache($temp);
        (new \Databases\QuiltsPermissions())->grantPermission($GLOBALS['auth']['id'], $mysqlInsertId, 'root');
    }

    header('Location: ./?s=quilts_moderate');
    die;

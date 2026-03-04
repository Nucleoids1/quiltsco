<?php
    require_once('../include/functions/create_quilt_image_cache.php');

    $_description = post('description');
    $_id = getInt('i');
    $_level = postInt('level');
    $_moderated = postInt('moderated');
    $_multiple = postInt('multiple');
    $_name = post('name');
    $_showAll = postInt('show_all');
    $_sidePixels = postInt('side_pixels');
    $_startAnywhere = postInt('start_anywhere');
    $_timeLimit = postInt('timelimit');
    $_workOnAll = postInt('work_on_all');

    if ($_level > 2 || $_level < 0)
    {
        $_level = 0;
    }

    $errors = '';

    $quiltsRow = (new \Databases\Quilts())->findById($_id);
    if (!$quiltsRow || $quiltsRow['finished']) {
        header('Location: ./?s=quilts_moderate');
        die;
    }

    if (strlen($_name) < 4)
    {
        $errors .= 'The name of the quilt must be 4 or more characters.<br />';
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
        include('../include/show/quilt_edit.php');
        die;
    }
    else
    {
        $quiltsPermissionsRow = (new \Databases\QuiltsPermissions())->findRootByUserAndQuilt($GLOBALS['auth']['id'], $_id);
        if ($quiltsPermissionsRow)
        {
            $timelimit = $_timeLimit * 60 * 60;
            (new \Databases\Quilts())->update($_id, $_name, $_description, $timelimit, $_sidePixels, $_level, $_showAll, $_workOnAll, $_multiple, $_startAnywhere, $_moderated);
            createQuiltImageCache($_id);
        }
    }

    header('Location: ./?s=quilts_moderate');
    die;

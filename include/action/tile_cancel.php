<?php
    $_id = getInt('i');
    $_x = getInt('x');
    $_y = getInt('y');

    (new \Databases\TilesPending())->deleteByQuiltAndMatrixAndUserId($_id, $_x, $_y, $GLOBALS['auth']['id']);

    header('Location: /?s=quilt&i=' . $_id);
    die;

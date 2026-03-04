<?php
    $_id = getInt('i');
    $_image = getInt('image');

    if ($GLOBALS['auth']['tracker'])
    {
        if ($_id && $_image)
        {
            (new \Databases\TrackerBugsImages())->deleteByImageIdAndTrackerId($_image, $_id);
        }
    }

    header('Location: ./?s=tracker_info&i=' . $_id);
    die;

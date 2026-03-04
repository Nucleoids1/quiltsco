<?php
    require_once('../include/functions/image_overall_rating.php');

    $_id = getInt('i');
    $_value = get('v');

    if ((new \Databases\Images())->findById($_id))
    {
        if ($_value == 1)
        {
            (new \Databases\ImagesRating())->upsertVote($GLOBALS['auth']['id'], $_id, 1);
        }
        elseif ($_value == 0)
        {
            (new \Databases\ImagesRating())->upsertVote($GLOBALS['auth']['id'], $_id, -1);
        }
    }

    if (get('r') == 'vote')
    {
        if ($_value == 1)
        {
            echo 'You Voted: <span class="notice_good">Good</span>';
        }
        elseif ($_value == 0)
        {
            echo 'You Voted: <span class="notice_error">Bad</span>';
        }
        else
        {
            echo '';
        }
    }
    else
    {
        echo imageOverallRating($_id);
    }

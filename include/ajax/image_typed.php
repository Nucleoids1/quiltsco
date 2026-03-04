<?php
    require_once('../include/functions/image_overall_type.php');

    $_id = getInt('i');
    $_vote = get('v');

    if ((new \Databases\Images())->findById($_id))
    {
        $imagesCategoriesRow = (new \Databases\ImagesCategories())->findById($_vote);
        if ($imagesCategoriesRow)
        {
            (new \Databases\ImagesCategoriesRating())->upsertRating($_id, $_vote, $GLOBALS['auth']['id']);
        }
        elseif ($_vote == '0')
        {
            (new \Databases\ImagesCategoriesRating())->deleteByImageIdAndUserId($_id, $GLOBALS['auth']['id']);
        }
    }

    echo imageOverallType($_id);

<?php
function imageOverallRating($id)
{
    $imagesRatingGoodCount = (new \Databases\ImagesRating())->countGoodByImageId($id);
    $imagesRatingBadCount = (new \Databases\ImagesRating())->countBadByImageId($id);
    return intval($imagesRatingGoodCount) >= intval($imagesRatingBadCount) ? (intval($imagesRatingGoodCount) == intval($imagesRatingBadCount) ? 'Neutral' : '<span class="notice_good">Good [+' . (intval($imagesRatingGoodCount) - intval($imagesRatingBadCount)) . ']</span>') : '<span class="notice_error">Bad [-' . (intval($imagesRatingBadCount) - intval($imagesRatingGoodCount)) . ']</span>';
}

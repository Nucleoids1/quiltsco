<?php
function imageOverallType($id)
{
    $voteCount = (new \Databases\ImagesCategoriesRating())->countByImageId($id);
    if ($voteCount) {
        $imagesCategoriesRatingRows = (new \Databases\ImagesCategoriesRating())->selectTopCategoriesByImageId($id, 2);
        $highest = $imagesCategoriesRatingRows[0];
        if (count($imagesCategoriesRatingRows) == 2) {
            $second = $imagesCategoriesRatingRows[1];
            $difference = intval($highest['num']) - intval($second['num']);
        } else {
            $difference = intval($highest['num']);
        }
        $imagesCategoriesRow = (new \Databases\ImagesCategories())->findById($highest['category_id']);
        return '<span class="' . ($imagesCategoriesRow['worksafe'] == 1 ? 'notice_good' : 'notice_error') . '">' . $imagesCategoriesRow['name'] . ' [' . $difference . ']</span>';
    } else {
        return 'No Type [0]';
    }
}

function imageOverallTypeId($id)
{
    $voteCount = (new \Databases\ImagesCategoriesRating())->countByImageId($id);
    if ($voteCount) {
        $highest = (new \Databases\ImagesCategoriesRating())->selectTopCategoryByImageId($id);
        return $highest['category_id'];
    } else {
        return 0;
    }
}

function imageOverallTypeWorksafe($id)
{
    $voteCount = (new \Databases\ImagesCategoriesRating())->countByImageId($id);
    if ($voteCount) {
        $highest = (new \Databases\ImagesCategoriesRating())->selectTopCategoryByImageId($id);
        $imagesCategoriesRow = (new \Databases\ImagesCategories())->findById($highest['category_id']);
        return $imagesCategoriesRow['worksafe'] == 1 ? true : false;
    } else {
        return true;
    }
}

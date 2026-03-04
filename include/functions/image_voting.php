<?php
require_once('../include/functions/image_overall_rating.php');
require_once('../include/functions/image_overall_type.php');

function imageVoting($id)
{
    echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;"><tr><td>';
    if ($GLOBALS['auth']['id']) {
        echo 'Rate Image: ';
        echo '<a href="javascript:ajaxPost(\'index.php?j=image_rate&i=' . safeJs($id) . '&v=1\', \'v_rating\'); ajaxPost(\'index.php?j=image_rate&i=' . safeJs($id) . '&v=1&r=vote\', \'vote_holder\');"><span class="notice_good">Good</span></a> / ';
        echo '<a href="javascript:ajaxPost(\'index.php?j=image_rate&i=' . safeJs($id) . '&v=0\', \'v_rating\'); ajaxPost(\'index.php?j=image_rate&i=' . safeJs($id) . '&v=0&r=vote\', \'vote_holder\');"><span class="notice_error">Bad</span></a>' . "\r\n";
        echo ' - <span id="vote_holder">';
        $imagesRatingRow = (new \Databases\ImagesRating())->findByUserIdAndImageId($GLOBALS['auth']['id'], $id);
        if ($imagesRatingRow) {
            echo 'You Voted: ';
            if ($imagesRatingRow['vote'] == 1) {
                echo '<span class="notice_good">Good</span>';
            } elseif ($imagesRatingRow['vote'] == -1) {
                echo '<span class="notice_error">Bad</span>';
            }
        }
        echo '</span> ';
    }
    echo 'Overall: <span id="v_rating">' . imageOverallRating($id) . '</span>';
    echo '</td><td style="text-align: right;">';
    if ($GLOBALS['auth']['id']) {
        $imagesCategoriesRatingRow = (new \Databases\ImagesCategoriesRating())->findByImageIdAndUserId($id, $GLOBALS['auth']['id']);
        $type = 0;
        if ($imagesCategoriesRatingRow) {
            $type = $imagesCategoriesRatingRow['category_id'];
        }
        echo 'Vote Type: <form style="margin: 0px; display: inline;">';
        echo '<select id="section" name="section" class="input_select" onChange="ajaxPost(\'index.php?j=image_typed&i=' . safeJs($id) . '&v=\' + document.getElementById(\'section\').value, \'v_type\');">';
        echo '<option value="0"' . ($type == 0 ? 'selected' : '') . '></option>';
        $imagesCategoriesRows = (new \Databases\ImagesCategories())->findAllOrderedByName();
        foreach ($imagesCategoriesRows as $imagesCategoriesRow) {
            echo '<option value="' . safeAttr($imagesCategoriesRow['id']) . '" ' . ($type == $imagesCategoriesRow['id'] ? 'selected ' : '') . 'class="' . ($imagesCategoriesRow['worksafe'] == 0 ? 'notice_error' : 'notice_good') . '">' . safeAttr($imagesCategoriesRow['name']) . ($imagesCategoriesRow['worksafe'] == 1 ? '' : ' - Not Worksafe') . '</option>';
        }
        echo '</select>';
        echo '</form>&nbsp;';
    }
    echo 'Overall: <span id="v_type">' . imageOverallType($id) . '</span>' . "\r\n";
    echo '</td></tr></table>';
}

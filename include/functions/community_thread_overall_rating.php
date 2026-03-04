<?php
function threadOverallRating($id)
{
    $communityThreadsRatingsCount = (new \Databases\CommunityThreadsRatings())->countByThreadId($id);
    if (intval($communityThreadsRatingsCount)) {
        $positive = (new \Databases\CommunityThreadsRatings())->countPositiveByThreadId($id);
        $negative = $communityThreadsRatingsCount - $positive;
        if ($positive >= $negative) {
            $score = $positive - $negative;
        } else {
            $score = $negative - $positive;
        }
        $communityThreadsRatingsRow = (new \Databases\CommunityThreadsRatings())->findTopCategoryStatsByThreadId($id);
        $communityThreadsCategoriesRow = (new \Databases\CommunityThreadsCategories())->findById((int)$communityThreadsRatingsRow['category_id']);
        if ($communityThreadsCategoriesRow['positive']) {
            return '<span class="notice_good">' . $communityThreadsCategoriesRow['name'] . ' [' . $score . ']</span>';
        } else {
            return '<span class="notice_error">' . $communityThreadsCategoriesRow['name'] . ' [-' . $score . ']</span>';
        }
    } else {
        return 'Unrated [0]';
    }
}

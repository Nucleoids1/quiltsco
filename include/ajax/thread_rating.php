<?php
    require_once('../include/functions/community_thread_overall_rating.php');

    $_id = get('i');
    $_vote = get('v');

    $communityThreadsRow = (new \Databases\CommunityThreads())->findById($_id);
    if ($communityThreadsRow)
    {
        if ($communityThreadsRow['thread_user_id'] != $GLOBALS['auth']['id'])
        {
            $communityThreadsCategoriesRow = (new \Databases\CommunityThreadsCategories())->findById((int)$_vote);
            if ($communityThreadsCategoriesRow)
            {
                (new \Databases\CommunityThreadsRatings())->upsertRating($_id, $_vote, $GLOBALS['auth']['id']);
            }
            elseif ($_vote == '0')
            {
                (new \Databases\CommunityThreadsRatings())->deleteByThreadIdAndUserId($_id, $GLOBALS['auth']['id']);
            }
        }
    }

    echo threadOverallRating($_id);

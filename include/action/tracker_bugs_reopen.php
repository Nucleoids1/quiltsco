<?php
    $_id = getInt('i');

    $trackerBugsRow = (new \Databases\TrackerBugs())->findById($_id);
    if (!$trackerBugsRow)
    {
        header('Location: ./?s=finished');
        die;
    }

    if ($trackerBugsRow['status'] == 250)
    {
        $trackerBugsCommentsRow = (new \Databases\Comments('tracker_bugs_comments'))->findLastByLinkId($_id);
        if ($trackerBugsCommentsRow)
        {
            if ($trackerBugsCommentsRow['user_id'] == $GLOBALS['auth']['id'])
            {
                (new \Databases\TrackerBugs())->reopen($_id);
            }
        }
    }

    header('Location: ./?s=tracker_bugs&i=' . $_id);
    die;

<?php
    $_id = get('i');

    $communityForumsRow = (new \Databases\CommunityForums())->findActiveForumContext($_id);
    if (!$communityForumsRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    $communityThreadsRows = (new \Databases\CommunityThreads())->selectRecentByForum($communityForumsRow['forum_id'], COMMUNITY_THREADS_PER_PAGE * 2);
    foreach ($communityThreadsRows as $communityThreadsRow)
    {
        //if ($communityThreadsRow['thread_sticky'] == 0)
        //{
            (new \Databases\CommunityThreadsPointers())->updateReadPointer($communityThreadsRow['thread_id'], $GLOBALS['auth']['id'], $communityThreadsRow['thread_last_message_id']);
        //}
    }

    header('Location: ./?s=community&i=' . $communityForumsRow['community_id']);
    die;

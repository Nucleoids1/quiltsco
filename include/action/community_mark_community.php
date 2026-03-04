<?php
    $_id = get('i');

    $communityRow = (new \Databases\Community())->findById($_id);
    if (!$communityRow)
    {
        header('Location: ./?s=communities');
        die;
    }

    $communitySectionsRows = (new \Databases\CommunitySections())->selectActiveByCommunity($communityRow['community_id']);
    foreach ($communitySectionsRows AS $communitySectionsRow)
    {
        $communityForumsRows = (new \Databases\CommunityForums())->selectActiveBySection($communitySectionsRow['section_id']);
        foreach ($communityForumsRows AS $communityForumsRow)
        {
            $communityThreadsRows = (new \Databases\CommunityThreads())->selectRecentByForum($communityForumsRow['forum_id'], COMMUNITY_THREADS_PER_PAGE * 2);
            foreach ($communityThreadsRows AS $communityThreadsRow)
            {
                //if ($communityThreadsRow['thread_sticky'] == 0)
                //{
                    (new \Databases\CommunityThreadsPointers())->upsertPointer($communityThreadsRow['thread_id'], $GLOBALS['auth']['id'], $communityThreadsRow['thread_last_message_id']);
                //}
            }
        }
    }

    header('Location: ./?s=community&i=' . $communityRow['community_id']);
    die;

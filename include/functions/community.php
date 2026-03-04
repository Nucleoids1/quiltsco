<?php
function updateCommunityThread($id, $todo = 'ABC')
{
    $communityThreadsRow = (new \Databases\CommunityThreads())->findByLegacyId($id);
    if ($communityThreadsRow) {
        if (strstr($todo, 'A') !== false) {
            $communityRepliesCount = (new \Databases\CommunityMessages())->countByThreadId($communityThreadsRow['id']);
            (new \Databases\CommunityThreads())->updateThreadRepliesCount($id, $communityRepliesCount);
        }
        if (strstr($todo, 'B') !== false) {
            $communityRepliesRow = (new \Databases\CommunityMessages())->findLegacyMinReplyIdByThreadId($communityThreadsRow['id']);
            if ($communityRepliesRow) {
                (new \Databases\CommunityThreads())->updateThreadFirstPostId($id, $communityRepliesRow['min']);
            }
        }
        if (strstr($todo, 'C') !== false) {
            $communityRepliesRow = (new \Databases\CommunityMessages())->findLegacyMaxReplyIdByThreadId($communityThreadsRow['id']);
            if ($communityRepliesRow) {
                (new \Databases\CommunityThreads())->updateThreadLastPostId($id, $communityRepliesRow['max']);
            }
        }
    }
}

function updateCommunitySection($community, $section, $todo = 'ABCD')
{
    $communityForumsRow = (new \Databases\CommunityForums())->findByLegacyId($section);
    if ($communityForumsRow) {
        if (strstr($todo, 'A') !== false) {
            $forumThreadsCount = (new \Databases\CommunityThreads())->countByLegacyForumId($communityForumsRow['id']);
            (new \Databases\CommunityForums())->updateForumThreadsCount($communityForumsRow['id'], $forumThreadsCount);
        }
        if (strstr($todo, 'B') !== false) {
            $forumRepliesCount = (new \Databases\CommunityMessages())->countByLegacyForumId($communityForumsRow['id']);
            (new \Databases\CommunityForums())->updateForumMessagesCount($communityForumsRow['id'], $forumRepliesCount);
        }
        if (strstr($todo, 'C') !== false) {
            $forumThreadsRow = (new \Databases\CommunityThreads())->findLegacyMaxForumThreadIdByForumId($communityForumsRow['id']);
            if ($forumThreadsRow) {
                (new \Databases\CommunityForums())->updateForumLastPostId($communityForumsRow['id'], $forumThreadsRow['max']);
            }
        }
        if (strstr($todo, 'D') !== false) {
            $forumRepliesRow = (new \Databases\CommunityMessages())->findLegacyMaxForumReplyIdByForumId($communityForumsRow['id']);
            if ($forumRepliesRow) {
                (new \Databases\CommunityForums())->updateForumLastPostId($communityForumsRow['id'], $forumRepliesRow['max']);
            }
        }
    }
}

function updateCommunityForum($id, $todo = 'ABCD')
{
    $communityForumsRow = (new \Databases\CommunityForums())->findByLegacyId($id);
    if ($communityForumsRow) {
        if (strstr($todo, 'A') !== false) {
            $forumThreadsCount = (new \Databases\CommunityThreads())->countByLegacyForumId($communityForumsRow['id']);
            (new \Databases\CommunityForums())->updateForumThreadsCount($communityForumsRow['id'], $forumThreadsCount);
        }
        if (strstr($todo, 'B') !== false) {
            $forumRepliesCount = (new \Databases\CommunityMessages())->countByLegacyForumId($communityForumsRow['id']);
            (new \Databases\CommunityForums())->updateForumMessagesCount($communityForumsRow['id'], $forumRepliesCount);
        }
        if (strstr($todo, 'C') !== false) {
            $forumThreadsRow = (new \Databases\CommunityThreads())->findLegacyMaxForumThreadIdByForumId($communityForumsRow['id']);
            if ($forumThreadsRow) {
                (new \Databases\CommunityForums())->updateForumLastPostId($communityForumsRow['id'], $forumThreadsRow['max']);
            }
        }
        if (strstr($todo, 'D') !== false) {
            $forumRepliesRow = (new \Databases\CommunityMessages())->findLegacyMaxForumReplyIdByForumId($communityForumsRow['id']);
            if ($forumRepliesRow) {
                (new \Databases\CommunityForums())->updateForumLastPostId($communityForumsRow['id'], $forumRepliesRow['max']);
            }
        }
    }
}

function updateAllCommunity()
{
    $communityForumsRows = (new \Databases\CommunityForums())->selectAllLegacyOrderedById();
    if ($communityForumsRow = reset($communityForumsRows)) {
        updateCommunityForum($communityForumsRow['id']);
    }
    $communityThreadsRows = (new \Databases\CommunityThreads())->selectAllLegacyOrderedById();
    if ($communityThreadsRow = reset($communityThreadsRows)) {
        updateCommunityThread($communityThreadsRow['id']);
    }
}

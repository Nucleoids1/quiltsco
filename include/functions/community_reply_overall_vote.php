<?php
function replyOverallRating($id)
{
    $goodVotesCount = (new \Databases\CommunityMessagesRating())->countByMessageAndVote($id, '1');
    $badVotesCount = (new \Databases\CommunityMessagesRating())->countByMessageAndVote($id, '-1');
    return 1 + intval($goodVotesCount) - intval($badVotesCount);
}

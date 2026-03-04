<?php
function communityReplyValid($_body)
{
    if (!$_body)
        return 'Your reply must have a body';
    if (strlen($_body) > COMMUNITY_REPLY_BODY_MAX)
        return 'Your reply cannot be more than ' . COMMUNITY_REPLY_BODY_MAX . ' characters long';

    return null;
}

<?php
function communityUpdateValid($_body)
{
    if (!$_body)
        return 'Your update must have a body';
    if (strlen($_body) > COMMUNITY_REPLY_BODY_MAX)
        return 'Your update cannot be more than ' . COMMUNITY_REPLY_BODY_MAX . ' characters long';

    return null;
}

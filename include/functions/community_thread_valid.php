<?php
function communityThreadValid($_title, $_body)
{
    if (!$_title)
        return 'Your message must have a subject';
    if (strlen($_title) > COMMUNITY_THREAD_TITLE_MAX)
        return 'Your subject cannot be more than ' . COMMUNITY_THREAD_TITLE_MAX . ' characters long';

    if (!$_body)
        return 'Your message must have a body';
    if (strlen($_body) > COMMUNITY_THREAD_BODY_MAX)
        return 'Your message cannot be more than ' . COMMUNITY_THREAD_BODY_MAX . ' characters long';

    return null;
}

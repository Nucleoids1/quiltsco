<?php
function communitySectionValid($_name)
{
    if (!$_name)
        return 'You must give your section a name.';
    if (strlen($_name) < COMMUNITY_SECTION_NAME_MIN)
        return 'Your section\'s name must be at least ' . COMMUNITY_SECTION_NAME_MIN . ' characters long.';
    if (strlen($_name) > COMMUNITY_SECTION_NAME_MAX)
        return 'Your section\'s name must be at most ' . COMMUNITY_SECTION_NAME_MAX . ' characters long.';
    else
        return null;
}

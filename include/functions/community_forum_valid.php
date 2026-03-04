<?php
function communityForumValid($_name, $_desc)
{
    if (!$_name) {
        return 'You must give your forum a name.';
    } elseif (strlen($_name) < COMMUNITY_FORUM_NAME_MIN) {
        return 'Your forum\'s name must be at least ' . COMMUNITY_FORUM_NAME_MIN . ' characters long.';
    } elseif (strlen($_name) > COMMUNITY_FORUM_NAME_MAX) {
        return 'Your forum\'s name must be at most ' . COMMUNITY_FORUM_NAME_MAX . ' characters long.';
    } elseif (strlen($_desc) < COMMUNITY_FORUM_DESC_MIN) {
        return 'Your forum\' description must be at least ' . COMMUNITY_FORUM_DESC_MIN . ' characters long.';
    } elseif (strlen($_desc) > COMMUNITY_FORUM_DESC_MAX) {
        return 'Your forum\'s description must be at most ' . COMMUNITY_FORUM_DESC_MAX . ' characters long.';
    }
    return '';
}

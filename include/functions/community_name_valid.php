<?php
function communityNameValid($_name)
{
    if (!$_name) {
        return 'You must give your community a name.';
    }
    if (strlen($_name) < COMMUNITY_NAME_MIN) {
        return 'Your community\'s name must be at least ' . COMMUNITY_NAME_MIN . ' characters long.';
    }
    if (strlen($_name) > COMMUNITY_NAME_MAX) {
        return 'Your community\'s name must be at most ' . COMMUNITY_NAME_MAX . ' characters long.';
    }
    if (!preg_match('/^[A-z0-9][\w\-\.\_ ]+$/', $_name)) {
        return 'Your community\'s name cannot have non alpha-numeric characters. (hyphens (-), underscores (_) and periods (.) are allowed)';
    }
    return null;
}

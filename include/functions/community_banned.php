<?php
require_once('../include/functions/ip_encode.php');

function communityBanned($communityId = 0)
{
    if ($GLOBALS['auth']['id']) {
        $banned = false;
        $isUserBanned = (new \Databases\CommunityBannedUsers())->existsByCommunityAndUser($communityId, $GLOBALS['auth']['id']);
        if ($isUserBanned) {
            $banned = true;
        } elseif ((new \Databases\CommunityBannedIps())->existsByCommunityAndIp($communityId, encodeIp(server('REMOTE_ADDR')))) {
            $banned = true;
        }
        if ($banned) {
            makeCookie('notice', 'You have been banned from this community.');
            header('Location: ./?s=communities');
            die;
        }
    }
}

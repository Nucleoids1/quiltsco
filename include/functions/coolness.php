<?php
function coolness($userId, $stats = 0)
{
    return 0;
    $communitiesThreadsCount = (new \Databases\CommunityThreads())->countByUserId($userId);
    $communitiesRepliesCount = (new \Databases\CommunityMessages())->countByUserId($userId);
    $galleryImagesCount = (new \Databases\GalleryImages())->countByUserId($userId);
    $friendsCount = (new \Databases\Friends())->countByFriendId($userId);
    $coolness = ($communitiesThreadsCount * 10) +
        ($communitiesRepliesCount * 5) +
        ($galleryImagesCount * 3) +
        ($friendsCount * 25);
    if ($stats == 1) {
        $return = array();
        $return['communities_threads'] = $communitiesThreadsCount * 10;
        $return['communities_replies'] = $communitiesRepliesCount * 5;
        $return['gallery_images'] = $galleryImagesCount * 3;
        $return['friends'] = $friendsCount * 25;
        $return['total'] = $coolness;
        return $return;
    } else {
        return $coolness;
    }
}

<?php
    require_once('../include/functions/community_banned.php');
    require_once('../include/functions/community_permissions.php');
    require_once('../include/functions/community_thread_valid.php');
    require_once('../include/functions/ip_encode.php');

    $_forumId = getInt('i');
    $_threadBody  = post('body');
    $_threadTitle = post('title');

    $communityForumsRow = (new \Databases\CommunityForums())->findById($_forumId);
    if (!$communityForumsRow || $communityForumsRow['forum_deleted'])
    {
        makeCookie('notice', 'Sorry, that forum does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    $communitySectionsRow = (new \Databases\CommunitySections())->findById($communityForumsRow['section_id']);
    if (!$communitySectionsRow || $communitySectionsRow['section_deleted'])
    {
        makeCookie('notice', 'Sorry, that forum does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    $communityRow = (new \Databases\Community())->findById($communitySectionsRow['community_id']);
    if (!$communityRow)
    {
        makeCookie('notice', 'Sorry, that forum does not exist.');
        header('Location: ./?s=communities');
        die;
    }

    $communityRowMerged = array_merge($communityForumsRow, $communitySectionsRow, $communityRow);

    communityBanned($communityRowMerged['community_id']);
    communityPermissions($communityRowMerged['community_id'], $communityRowMerged['section_id'], $communityRowMerged['forum_id']);

    if ($communityRowMerged['forum_locked'] != 0 && !$GLOBALS['auth']['community']['locked_post'])
    {
        makeCookie('notice', 'Sorry, that forum is locked. No Posting allowed.');
        header('Location: ./?s=communities');
        die;
    }

    if ($communityRowMerged['forum_automated'] != 0 && !$GLOBALS['auth']['community']['automate_post'])
    {
        makeCookie('notice', 'Sorry, that forum is locked. No Posting allowed.');
        header('Location: ./?s=communities');
        die;
    }

    $error = communityThreadValid($_threadTitle, $_threadBody);
    if ($error)
    {
        makeCookie('notice', $error);
        header('Location: ./?s=communities');
        die;
    }

    $mood = 0;
    $membersMoodsRow = (new \Databases\MembersMoods())->findLatestByUser($GLOBALS['auth']['id']);
    if ($membersMoodsRow)
    {
        $mood = $membersMoodsRow['id'];
    }

    $messageId = (new \Databases\CommunityMessages())->addReply(
        0,
        $GLOBALS['auth']['id'],
        encodeIp(server('REMOTE_ADDR')),
        $mood
    );
    (new \Databases\CommunityMessagesBodies())->addMessageBody($messageId, $_threadBody);
    $threadId = (new \Databases\CommunityThreads())->createThread(
        $communityRowMerged['forum_id'],
        $GLOBALS['auth']['id'],
        $_threadTitle,
        $messageId
    );
    (new \Databases\CommunityMessages())->assignThread($messageId, $threadId);

    (new \Databases\CommunityThreadsPointers())->updateReadPointer($threadId, $GLOBALS['auth']['id'], $messageId);

    (new \Databases\CommunityForums())->updateForumStats(
        $communityRowMerged['forum_id'],
        (new \Databases\CommunityThreads())->sumMessagesByForum($communityRowMerged['forum_id']),
        (new \Databases\CommunityThreads())->countByForum($communityRowMerged['forum_id'])
    );

    header('Location: ./?s=community_forum&i=' . $communityRowMerged['forum_id']);
    die;

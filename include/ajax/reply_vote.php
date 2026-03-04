<?php
    require_once('../include/functions/community_reply_overall_vote.php');

    $_id = intval(get('i'));
    $_vote = get('v');

    $communityMessagesRow = (new \Databases\CommunityMessages())->findById($_id);
    if ($communityMessagesRow)
    {
        if ($communityMessagesRow['message_user_id'] != $GLOBALS['auth']['id'])
        {
            if ($_vote == 'good')
            {
                (new \Databases\CommunityMessagesRating())->upsertVote($_id, $GLOBALS['auth']['id'], '1');
            }
            elseif ($_vote == 'bad')
            {
                (new \Databases\CommunityMessagesRating())->upsertVote($_id, $GLOBALS['auth']['id'], '-1');
            }
        }
    }

    $replyOverallRating = replyOverallRating($_id);

    $actions = [];
    if ($replyOverallRating == 0)
    {
        $actions[] = [
            'action' => 'update',
            'id' => 'r' . $_id,
            'content' => 'Neutral [0]'
        ];
        $actions[] = [
            'action' => 'show',
            'id' => 'i' . $_id,
            'display' => 'block'
        ];
    }
    elseif ($replyOverallRating > 0)
    {
        $actions[] = [
            'action' => 'update',
            'id' => 'r' . $_id,
            'content' => '<span class="notice_good">Good [+' . $replyOverallRating . ']</span>'
        ];
        $actions[] = [
            'action' => 'show',
            'id' => 'i' . $_id,
            'display' => 'block'
        ];
    }
    else
    {
        $actions[] = [
            'action' => 'update',
            'id' => 'r' . $_id,
            'content' => '<span class="notice_error">Bad [' . $replyOverallRating . ']</span>'
        ];
        $actions[] = [
            'action' => 'hide',
            'id' => 'i' . $_id
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['actions' => $actions]);

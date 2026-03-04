<?php
    $_id = getInt('i');

    $communityMessagesRow = (new \Databases\CommunityMessages())->findWithBodyById($_id);
    if ($communityMessagesRow)
    {
        $message = "[quote][b]Originally Posted By " . htmlentities(getUsername($communityMessagesRow['message_user_id'])) . "[/b]\r\n" . htmlentities($communityMessagesRow['message_body'])."\r\n[/quote]\r\n";
        echo $message;
    }

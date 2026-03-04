<?php
    set_time_limit(0);

    (new \Databases\MessagesIndex())->truncateTable();

    $messagesRows = (new \Databases\Messages())->findAllOrderedById();
    foreach ($messagesRows as $messagesRow)
    {
        $messagesIndexRow = (new \Databases\MessagesIndex())->findBySenderAndReceiver($messagesRow['sender_id'], $messagesRow['recipiant_id']);
        if ($messagesIndexRow)
        {
            if ($messagesIndexRow['last_received'] <= $messagesRow['posted_on'])
            {
                (new \Databases\MessagesIndex())->updateLastReceived($messagesRow['sender_id'], $messagesRow['recipiant_id'], $messagesRow['posted_on'], $messagesRow['id']);
            }
        }
        else
        {
            (new \Databases\MessagesIndex())->indexReceivedMessage($messagesRow['sender_id'], $messagesRow['recipiant_id'], $messagesRow['posted_on'], $messagesRow['id']);
        }

        $messagesIndexRow = (new \Databases\MessagesIndex())->findByReceiverAndSender($messagesRow['sender_id'], $messagesRow['recipiant_id']);
        if ($messagesIndexRow)
        {
            if ($messagesIndexRow['last_sent'] <= $messagesRow['posted_on'])
            {
                (new \Databases\MessagesIndex())->updateLastSent($messagesRow['sender_id'], $messagesRow['recipiant_id'], $messagesRow['posted_on'], $messagesRow['id']);
            }
        }
        else
        {
            (new \Databases\MessagesIndex())->indexSentMessage($messagesRow['sender_id'], $messagesRow['recipiant_id'], $messagesRow['posted_on'], $messagesRow['id']);
        }
    }

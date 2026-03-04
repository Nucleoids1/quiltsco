<?php
    if ($GLOBALS['auth']['root'])
    {
        $messagesOldRows = (new \Databases\Messages())->selectOldMessages();
        foreach ($messagesOldRows as $messagesOldRow)
        {
            (new \Databases\Messages())->migrateMessage(
                $messagesOldRow['sender_id'],
                $messagesOldRow['recipiant_id'],
                $messagesOldRow['body'],
                $messagesOldRow['posted_on'],
                $messagesOldRow['viewed']
            );
        }
    }

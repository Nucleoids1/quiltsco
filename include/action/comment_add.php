<?php
    $_back = decodeUrlPath(get('b'), 's=finished');
    $_comment = post('comment');
    $_link = postInt('link');
    $_table = post('table');

    if (!in_array($_table, ['tracker_bugs_comments', 'tiles_comments', 'quilts_comments', 'images_comments', 'members_comments']))
    {
        header('Location: ./?' . $_back);
        die;
    }

    if ($GLOBALS['auth']['id'])
    {
        if (!$_comment)
        {
            makeCookie('notice', 'You cannot post a blank message.');
            header('Location: ./?' . $_back);
            die;
        }
        $tableChecker = str_replace('_comments', '', $_table);
        $linkExists = (new Databases\Comments($tableChecker))->findById($_link) ? 1 : 0;
        if ($linkExists)
        {
            (new \Databases\Comments($_table))->addComment($_link, $GLOBALS['auth']['id'], substr($_comment, 0, 1024));
        }
        else
        {
            makeCookie('notice', 'Sorry, but you can\'t comment on something that doesn\'t exist.');
        }
    }

    header('Location: ./?' . $_back);
    die;

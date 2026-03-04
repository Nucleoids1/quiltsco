<?php
    $_back = decodeUrlPath(get('b'), 's=finished');
    $_id = getInt('i');
    $_link = getInt('link');
    $_table = get('table');

    if (!in_array($_table, ['tracker_bugs_comments', 'tiles_comments', 'quilts_comments', 'images_comments', 'members_comments']))
    {
        return;
    }

    if ($GLOBALS['auth']['id'])
    {
        $tableChecker = str_replace('_comments', '', $_table);
        $linkExists = (new Databases\Comments($tableChecker))->findById($_link) ? 1 : 0;
        if ($linkExists)
        {
            if ($GLOBALS['auth']['root'])
            {
                (new \Databases\Comments($_table))->deleteComment($_id, $_link);
            }
            else
            {
                (new \Databases\Comments($_table))->deleteCommentByUser($_id, $_link, $GLOBALS['auth']['id']);
            }
        }
        else
        {
            makeCookie('notice', 'Sorry, but you can\'t delete a comment that doesn\'t exist.');
        }
    }

    header('Location: ./?' . $_back);
    die;

<?php
    $_id = getInt('i');
    $_which = get('w');

    $actions = [];

    if ((new \Databases\Members())->findById($_id))
    {
        if ($_which == 'make')
        {
            (new \Databases\Friends())->addFriend($GLOBALS['auth']['id'], $_id);
            $actions[] = ['id' => 'friend', 'action' => 'update', 'content' => makeLink('javascript:removeFriend();', 'Remove Friend')];
        }
        elseif ($_which == 'remove')
        {
            (new \Databases\Friends())->deleteByUserIdAndFriendId($GLOBALS['auth']['id'], $_id);
            $actions[] = ['id' => 'friend', 'action' => 'update', 'content' => makeLink('javascript:makeFriend();', 'Make Friend')];
        }
    }

    $friendsCount = (new \Databases\Friends())->countByUserId($GLOBALS['auth']['id']);
    $actions[] = ['id' => 'friends', 'action' => 'update', 'content' => (string) $friendsCount];

    header('Content-Type: application/json');
    echo json_encode(['actions' => $actions]);

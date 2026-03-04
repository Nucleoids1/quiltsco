<?php
    $_cache = post('cache');

    $membersCreateRow = (new \Databases\MembersCreate())->findByUserIdAndCache($GLOBALS['auth']['id'], $_cache);
    if ($membersCreateRow)
    {
        (new \Databases\Members())->updateEmail($GLOBALS['auth']['id'], $membersCreateRow['email']);
        (new \Databases\MembersCreate())->deleteByUserIdAndCache($GLOBALS['auth']['id'], $membersCreateRow['cache']);
        makeCookie('login_email', $membersCreateRow['email']);
    }

    header('Location: ./?s=userinfo');
    die;

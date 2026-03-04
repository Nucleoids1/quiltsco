<?php
    if ($GLOBALS['auth']['id'])
    {
        removeMemberOnlineSession(cookie('sha1'), cookie('sha2'), $GLOBALS['auth']['id'], true);
    }
    killCookie('sha1');
    killCookie('sha2');
    killCookie('login_email');
    killCookie('login_password');
    header('Location: ./?' . $_back);
    die;

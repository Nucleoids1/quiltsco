<?php
    require_once('../include/functions/send_email.php');

    $_back = decodeUrlPath(get('b'), 's=finished');

    if ((int) $GLOBALS['auth']['id'] !== 1)
    {
        redirectWithNotice('Access denied.', './?' . $_back);
    }

    if (!$GLOBALS['auth']['email'])
    {
        redirectWithNotice('No email address is set for this account.', './?' . $_back);
    }

    $subject = 'Quilts Community - Test Email';
    $body = 'This is a test email triggered by user #1 on ' . date('Y-m-d H:i:s') . '.';
    sendEmail($GLOBALS['auth']['email'], $subject, $body);

    redirectWithNotice('Test email sent to ' . $GLOBALS['auth']['email'] . '.', './?' . $_back);

<?php
require_once('../include/functions/closest_word.php');
require_once('../include/functions/pages.php');
require_once('../include/functions/word_wrap_new.php');

$GLOBALS['highlight'] = 'userinfo';


$_error = get('e');
$_page = getInt('p', 1);
$_user = get('u');

if ($_user) {
    $membersRow = (new \Databases\Members())->findById(getUserId($_user));
    if ($membersRow) {
        $userId = $membersRow['id'];
        $userName = $membersRow['username'];
    } else {
        makeCookie('notice', 'Sorry, we do not have any members by that name.');
        header('Location: ./?s=messages');
        die;
    }
} else {
    $userId = '';
    $userName = '';
}


include('../include/parts/header.php');

if ($userId) {
    (new \Databases\MessagesEmail())->deleteByUserIdAndLinkId($GLOBALS['auth']['id'], $userId);
    (new \Databases\Messages())->markConversationViewed($userId, $GLOBALS['auth']['id']);
    $membersRow = (new \Databases\Members())->findById($userId);
    $userImageId = $membersRow['main_image_id'];
    $historyCount = (new \Databases\Messages())->countConversationVisibleToUser($userId, $GLOBALS['auth']['id']);
    $pages = '';
    if ($historyCount > MESSAGES_PER_PAGE) {
        $pageCount = ceil($historyCount / MESSAGES_PER_PAGE);
        $pages = '<span class="pages">Page: ' . pages('?s=messages&u=' . safeAttr($_user), $_page, $pageCount) . '</span>';
    }
    echo boxOutsideTop('<a href="?s=messages">Your Conversations</a> - With <a href="' . safeUrl('?s=profile&u=' . $_user) . '">' . safeAttr($_user) . '</a>' . ($pages ? '<br />' . $pages : ''));
    if ($_error) {
        echo '<div class="content"><span class="notice_error" style="font-size: 1.0rem; font-weight: bold;">';
        if ($_error == 'body_missing') {
            echo "Sorry, you need to enter a message to send.";
        } elseif ($_error == 'double_post') {
            echo "Sorry, you cannot send the same message twice in a row.";
        } elseif ($_error == 'rapid') {
            echo "Sorry, you must wait more time between sending messages.";
        } elseif ($_error == 'security') {
            echo "Sorry, you have entered the incorrect security code.";
        }
        echo '</span></div>';
    }
    echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
    echo '<td style="width: ' . (THUMB_WIDTH) . 'px; vertical-align: top;">';
    echo boxImageTop('margin: 0px 5px 0px 0px;');
    echo '<a href="' . safeUrl('?s=profile&u=' . $_user) . '"><img src="?g=thumb&i=' . getMainImageId(getUserId($_user)) . '" alt="Profile photo of ' . safeAttr($_user) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a>';
    echo boxImageBottom();
    echo '</td>';
    echo '<td style="vertical-align: top;" class="inside">';
    echo '<form action="' . safeUrl('?a=messages&u=' . $_user) . '" method="post" class="form">';
    echo csrfField();
    echo '<label for="message_body" style="position: absolute; left: -9999px;">Message</label><textarea name="body" id="message_body" class="input_text" style="width: 90%; height: 112px;"></textarea>';
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    $userCountry = ip2country();
    if ($userCountry != 'CA' && $userCountry != 'US') {
        echo '<table style="border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        echo '<td><img src="?s=security_code&rand=' . random_int(intval('1' . str_repeat('0', strlen(strval(PHP_INT_MAX)) - 1)), PHP_INT_MAX) . '" alt="Security code - enter the characters shown" style="border: 0; vertical-align: middle;"></td>';
        echo '<td style="padding-left: 10px;"><label for="security_code">Security Code:</label> <input type="text" name="security_code" id="security_code" class="input_text" style="width: 100px;"></td>';
        echo '</tr></table>';
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    }
    echo '<input type="submit" value="Send Message" class="input_submit">';
    echo '</td>';
    echo '</tr></table>';
    $messagesHistoryRows = (new \Databases\Messages())->selectConversationVisibleToUserPage($userId, $GLOBALS['auth']['id'], (($_page - 1) * MESSAGES_PER_PAGE), MESSAGES_PER_PAGE);
    if ($historyCount > 0) {
        foreach ($messagesHistoryRows as $messagesHistoryRow) {
            if ($messagesHistoryRow['sender_id'] == $GLOBALS['auth']['id']) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
                echo '<td style="vertical-align: top;" class="inside">';
                echo '<span style="font-weight: bold;">' . niceDate($messagesHistoryRow['posted_on'], 'M j Y @ g:ia') . '</span>';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo nl2br(wordWrapNew(safeHtml($messagesHistoryRow['body']), 1));
                echo '</td>';
                echo '<td style="width: ' . THUMB_WIDTH . 'px; vertical-align: top;">';
                echo boxImageTop('margin: 0px 0px 0px 5px;');
                echo '<a href="' . safeUrl('?s=profile&u=' . $GLOBALS['auth']['username']) . '"><img src="?g=thumb&i=' . getMainImageId(getUserId($GLOBALS['auth']['username'])) . '" alt="Profile photo of ' . safeAttr($GLOBALS['auth']['username']) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a>';
                echo boxImageBottom();
                echo '</td>';
                echo '</tr></table>';
            } else {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
                echo '<td style="width: ' . THUMB_WIDTH . 'px; vertical-align: top;">';
                echo boxImageTop('margin: 0px 5px 0px 0px;');
                echo '<a href="' . safeUrl('?s=profile&u=' . $_user) . '"><img src="?g=thumb&i=' . getMainImageId(getUserId($_user)) . '" alt="Profile photo of ' . safeAttr($_user) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a>';
                echo boxImageBottom();
                echo '</td>';
                echo '<td style="vertical-align: top;" class="inside">';
                echo '<span style="font-weight: bold;">' . niceDate($messagesHistoryRow['posted_on'], 'M j Y @ g:ia') . '</span>';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo nl2br(wordWrapNew(safeHtml($messagesHistoryRow['body']), 1));
                echo '</td>';
                echo '</tr></table>';
            }
        }
    }
    echo boxOutsideBottom();
    if ($pages) {
        echo makePages('<a href="?s=messages">Your Conversations</a> - With <a href="' . safeUrl('?s=profile&u=' . $_user) . '">' . safeAttr($_user) . '</a><br />' . $pages);
    }
} else {
    $messagesIndexCount = (new \Databases\MessagesIndex())->countInboxVisibleToUser($GLOBALS['auth']['id']);
    $countPage = ceil($messagesIndexCount / MAIL_USERS_PER_PAGE);
    $pages = '<span class="pages">Page: ' . pages('?s=messages', $_page, $countPage) . '</span>';
    echo boxOutsideTop('Your Conversations' . ($countPage ? '<br />' . $pages : ''));
    if ($countPage) {
        $i = 0;
        $messagesIndexRows = (new \Databases\MessagesIndex())->selectInboxVisibleToUserPage($GLOBALS['auth']['id'], (($_page - 1) * MAIL_USERS_PER_PAGE), MAIL_USERS_PER_PAGE);
        foreach ($messagesIndexRows as $messagesIndexRow) {
            if ($i++) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            $username = getUsername($messagesIndexRow['sender_id']);
            $messagesNewCount = (new \Databases\Messages())->countUnreadBySenderAndRecipient($messagesIndexRow['sender_id'], $GLOBALS['auth']['id']);
            echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
            echo '<td style="width: 90px; vertical-align: top;">';
            echo boxImageTop('margin: 0px 5px 0px 0px;');
            echo '<a href="' . safeUrl('?s=messages&u=' . $username) . '"><img src="?g=thumb&i=' . getMainImageId($messagesIndexRow['sender_id']) . '" alt="Profile photo of ' . safeAttr($username) . '" style="width: 80px; height: 60px; border: 0px;"></a>';
            echo boxImageBottom();
            echo '</td>';
            echo '<td style="vertical-align: top;" class="inside">';
            echo '<span style="font-size: 1.0rem; font-weight: bold;">' . makeLink(safeUrl('?s=messages&u=' . $username), safeAttr($username) . ' @ ' . ($messagesIndexRow['last_received'] == '0000-00-00 00:00:00' ? 'Waiting For Reply' : niceDate($messagesIndexRow['last_received'], 'M j Y, g:ia')));
            echo $messagesNewCount > 0 ? ' [NEW]' : '';
            echo '</span><br />';
            $messagesRow = null;
            if ($messagesIndexRow['message_id_received'] > 0) {
                $messagesRow = (new \Databases\Messages())->findById($messagesIndexRow['message_id_received']);
            } elseif ($messagesIndexRow['message_id_sent'] > 0) {
                $messagesRow = (new \Databases\Messages())->findById($messagesIndexRow['message_id_sent']);
            }
            if ($messagesRow) {
                echo wordWrapNew(safeHtml(closestWord($messagesRow['body'], 400)), 0);
            }
            echo '</td>';
            echo '</table>';
        }
    } else {
        echo boxInsideTop();
        echo 'You don\'t have any conversations. Why not start one?';
        echo boxInsideBottom();
    }
    echo boxOutsideBottom();
    if ($countPage) {
        echo makePages($pages);
    }
}

include('../include/parts/footer.php');

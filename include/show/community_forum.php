<?php
require_once('../include/functions/community_banned.php');
require_once('../include/functions/community_permissions.php');
require_once('../include/functions/community_thread_overall_rating.php');
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'forum';


$_id = getInt('i');
$_page = getInt('p', 1);

if (language() == 'french') {
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_FORUM', 'Désolé, ce forum n\'existe pas.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_SECTION', 'Désolé, cette section n\'existe pas.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_COMMUNITY', 'Désolé, cette communauté n\'existe pas.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_THREADS_YET', 'Il n\'y a pas de sujet dans ce forum pourtant!');
    define('SHOW_COMMUNITY_FORUM_MESSAGES', 'Messages');
    define('SHOW_COMMUNITY_FORUM_PAGE', 'Page');
    define('SHOW_COMMUNITY_FORUM_LAST_UPDATED', 'Mis à jour');
    define('SHOW_COMMUNITY_FORUM_STICKIES', 'Stickies');
    define('SHOW_COMMUNITY_FORUM_MESSAGES_TOTAL', 'threads avec');
    define('SHOW_COMMUNITY_FORUM_POST_NEW_MESSAGE', 'Poster Un Nouveau Message');
    define('SHOW_COMMUNITY_FORUM_SUBJECT', 'Sujet');
    define('SHOW_COMMUNITY_FORUM_BODY', 'Message');
    define('SHOW_COMMUNITY_FORUM_LOCKED', 'This forum is locked. No new messages are allowed.');
    define('SHOW_COMMUNITY_FORUM_MUST_BE_LOGGED_IN', 'You must be logged in to post a message.');
    define('SHOW_COMMUNITY_FORUM_MODERATOR_TOOLS', 'Moderator Tools');
    define('SHOW_COMMUNITY_FORUM_LOCK_THIS_FORUM', 'Lock This Forum');
    define('SHOW_COMMUNITY_FORUM_UNLOCK_THIS_FORUM', 'Unlock This Forum');
    define('SHOW_COMMUNITY_FORUM_BY', 'par');
    define('SHOW_COMMUNITY_FORUM_TOTAL', 'totaux');
    define('SHOW_COMMUNITY_FORUM_NEW', 'nouveau');
    define('SHOW_COMMUNITY_FORUM_VIEWS', 'Vues');
    define('SHOW_COMMUNITY_FORUM_REPLIES', 'Réponses');
    define('SHOW_COMMUNITY_FORUM_REPLIES_LC', 'message totalisent');
    define('SHOW_COMMUNITY_FORUM_AUTOMATED', 'This forum is automated. No new messages are allowed.');
} else {
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_FORUM', 'Sorry, that forum does not exist.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_SECTION', 'Sorry, that section does not exist.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_COMMUNITY', 'Sorry, that community does not exist.');
    define('SHOW_COMMUNITY_FORUM_ERROR_NO_THREADS_YET', 'There aren\'t any threads in this forum yet!');
    define('SHOW_COMMUNITY_FORUM_MESSAGES', 'Messages');
    define('SHOW_COMMUNITY_FORUM_PAGE', 'Page');
    define('SHOW_COMMUNITY_FORUM_LAST_UPDATED', 'Last Updated');
    define('SHOW_COMMUNITY_FORUM_STICKIES', 'Stickies');
    define('SHOW_COMMUNITY_FORUM_MESSAGES_TOTAL', 'threads with');
    define('SHOW_COMMUNITY_FORUM_POST_NEW_MESSAGE', 'Post New Message');
    define('SHOW_COMMUNITY_FORUM_SUBJECT', 'Subject');
    define('SHOW_COMMUNITY_FORUM_BODY', 'Body');
    define('SHOW_COMMUNITY_FORUM_LOCKED', 'This forum is locked. No new messages are allowed.');
    define('SHOW_COMMUNITY_FORUM_MUST_BE_LOGGED_IN', 'You must be logged in to post a message.');
    define('SHOW_COMMUNITY_FORUM_MODERATOR_TOOLS', 'Moderator Tools');
    define('SHOW_COMMUNITY_FORUM_LOCK_THIS_FORUM', 'Lock This Forum');
    define('SHOW_COMMUNITY_FORUM_UNLOCK_THIS_FORUM', 'Unlock This Forum');
    define('SHOW_COMMUNITY_FORUM_BY', 'by');
    define('SHOW_COMMUNITY_FORUM_TOTAL', 'total');
    define('SHOW_COMMUNITY_FORUM_NEW', 'new');
    define('SHOW_COMMUNITY_FORUM_VIEWS', 'Views');
    define('SHOW_COMMUNITY_FORUM_REPLIES', 'Replies');
    define('SHOW_COMMUNITY_FORUM_REPLIES_LC', 'messages total');
    define('SHOW_COMMUNITY_FORUM_AUTOMATED', 'This forum is automated. No new messages are allowed.');
}

$communityForumsRow = (new \Databases\CommunityForums())->findActiveForumContext($_id);
if (!$communityForumsRow) {
    header('Location: ./?s=communities');
    die;
}

communityBanned($communityForumsRow['community_id']);
communityPermissions($communityForumsRow['community_id'], $communityForumsRow['section_id'], $communityForumsRow['forum_id']);

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=communities">Communities</a> - <a href="?s=community&i=' . $communityForumsRow['community_id'] . '">' . $communityForumsRow['community_name'] . '</a> - ' . $communityForumsRow['forum_name_' . language()], '[ <a href="?s=community_my_forum&i=' . $communityForumsRow['community_id'] . '&type=new">New Threads</a> | <a href="?s=community_my_forum&i=' . $communityForumsRow['community_id'] . '&type=my_new">Participating New Threads</a> ]');
echo boxInsideTop();
echo '<span style="font-size: 1.05rem; font-weight: bold;">' . htmlentities($communityForumsRow['forum_name_' . language()]);
if ($communityForumsRow['forum_locked'] == 1) {
    echo ' <span class="notice_attention">Locked</span>';
}
echo '</span><br /><span style="font-size: 1.0rem;">' . htmlentities($communityForumsRow['forum_description_' . language()]) . '</span>';
echo boxInsideBottom();
echo boxOutsideBottom();

$pageCount = ceil($communityForumsRow['forum_threads'] / COMMUNITY_THREADS_PER_PAGE);
$pages = '<span class="pages">Page: ' . pages('?s=community_forum&i=' . $communityForumsRow['forum_id'], $_page, $pageCount, true) . '</span>';

$communityThreadsRows = (new \Databases\CommunityThreads())->selectPageByForum($communityForumsRow['forum_id'], (($_page - 1) * COMMUNITY_THREADS_PER_PAGE), COMMUNITY_THREADS_PER_PAGE);
if (count($communityThreadsRows)) {
    echo boxOutsideTop('<a href="?s=communities">Communities</a> - <a href="?s=community&i=' . $communityForumsRow['community_id'] . '">' . $communityForumsRow['community_name'] . '</a> - ' . $communityForumsRow['forum_name_' . language()] . '<br />' . $pages, '[ <a href="?s=community_help">Help</a>' . ($GLOBALS['auth']['id'] ? ' | ' . makePostLink('?a=community_mark_forum&i=' . $communityForumsRow['forum_id'], 'Mark All As Viewed') . '' : '') . ' ]');
    $i = 0;
    $stickiesHeader = 0;
    $normalHeader = 0;
    foreach ($communityThreadsRows as $communityThreadsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo boxInsideTop();
        $status = '';
        if ($communityThreadsRow['thread_locked']) {
            $status .= ' <span class="notice_attention">Locked</span>';
        }
        $newReplyCount = 0;
        $newThread = 0;
        if ($GLOBALS['auth']['id']) {
            $communityThreadsPointersRow = (new \Databases\CommunityThreadsPointers())->findByUserAndThread($GLOBALS['auth']['id'], $communityThreadsRow['thread_id']);
            if ($communityThreadsPointersRow) {
                $newReplyCount = (new \Databases\CommunityMessages())->countByThreadIdAfterMessage($communityThreadsRow['thread_id'], $communityThreadsPointersRow['message_id']);
            } else {
                $newReplyCount = $communityThreadsRow['thread_messages'];
            }
            if ($newReplyCount) {
                $newThread = 1;
            }
        }
        echo '<div style="cursor: pointer;" role="link" tabindex="0" onClick="document.location=\'?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l\';" onKeyDown="if(event.key===\'Enter\'||event.key===\' \'||event.code===\'Space\'){event.preventDefault();document.location=\'?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l\';}">';
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        if ($communityThreadsRow['thread_sticky']) {
            echo '<td class="notice_error" style="width: 40px; font-weight: bold; text-align: center;">sticky</td>';
        }
        if ($newThread) {
            echo '<td class="notice_attention" style="width: 40px; font-weight: bold; text-align: center;">' . SHOW_COMMUNITY_FORUM_NEW . '</td>';
        }
        if ($GLOBALS['auth']['id']) {
            if ($communityThreadsRow['thread_user_id'] == $GLOBALS['auth']['id']) {
                $myReplyCount['num'] = 1;
            } else {
                $myReplyCount['num'] = (new \Databases\CommunityMessages())->countByUserAndThreadId($GLOBALS['auth']['id'], $communityThreadsRow['thread_id']);
            }
        } else {
            $myReplyCount['num'] = 0;
        }
        $newtitle = '';
        if ($communityThreadsRow['thread_tile']) {
            $tilesRow = (new \Databases\Tiles())->findById($communityThreadsRow['thread_tile']);
            if ($tilesRow) {
                if ($tilesRow['comment']) {
                    $newtitle = substr($tilesRow['comment'], 0, 50);
                }
            } else {
                $newtitle = 'Removed Tile';
            }
        }
        $newquilt = '';
        if ($communityThreadsRow['thread_quilt']) {
            $quiltsRow = (new \Databases\Quilts())->findById($communityThreadsRow['thread_quilt']);
            if ($quiltsRow) {
                if ($quiltsRow['name']) {
                    $newquilt = substr($quiltsRow['name'], 0, 50);
                }
            } else {
                $newquilt = 'Removed Quilt';
            }
        }
        if ($communityThreadsRow['thread_tile']) {
            $threadLink = makeLink('?s=tile&i=' . $communityThreadsRow['thread_tile'] . '#l', htmlentities($newtitle ? $newtitle : $communityThreadsRow['thread_title']));
        } elseif ($communityThreadsRow['thread_quilt']) {
            $threadLink = makeLink('?s=quilt&i=' . $communityThreadsRow['thread_quilt'] . '#l', htmlentities($newquilt ? $newquilt : $communityThreadsRow['thread_title']));
        } else {
            $threadLink = makeLink('?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l', htmlentities($communityThreadsRow['thread_title']));
        }
        echo '<td style="padding-left: 8px;"><div style="font-weight: bold; font-size: 1.0rem; display: inline;"' . ($myReplyCount['num'] ? ' class="notice_good"' : ' class="notice_error"') . '>&raquo;</div>&nbsp;<div style="font-weight: bold; font-size: 1.0rem; display: inline;">' . $threadLink . ($status ? $status : '') . '</div><div>' . SHOW_COMMUNITY_FORUM_BY . ': ' . getUsername($communityThreadsRow['thread_user_id'], 1) . '</div></td>';
        echo '<td style="width: 80px; padding-right: 5px; text-align: right; vertical-align: top;">' . SHOW_COMMUNITY_FORUM_VIEWS . ':</td>';
        echo '<td style="width: 130px; vertical-align: top;"><b>' . ($communityThreadsRow['thread_views'] > 1000 ? '<span class="notice_error">' : '') . number_format($communityThreadsRow['thread_views']) . ($communityThreadsRow['thread_views'] > 1000 ? '</span>' : '') . '</b><br />' . threadOverallRating($communityThreadsRow['thread_id']) . '</td>';
        echo '<td style="width: 80px; padding-right: 5px; text-align: right; vertical-align: top;">' . SHOW_COMMUNITY_FORUM_REPLIES . ':</td>';
        echo '<td style="width: 110px; vertical-align: top;">';
        if ($GLOBALS['auth']['id']) {
            if ($newReplyCount) {
                echo '<span class="notice_attention"><b>' . number_format($newReplyCount) . '</b> ' . SHOW_COMMUNITY_FORUM_NEW . '</span>';
            } else {
                echo '<b>' . number_format($newReplyCount) . '</b> ' . SHOW_COMMUNITY_FORUM_NEW;
            }
            echo '<br />';
        }
        echo '<b>' . ($communityThreadsRow['thread_messages'] > 100 ? '<span class="notice_error">' : '') . number_format($communityThreadsRow['thread_messages']) . ($communityThreadsRow['thread_messages'] > 100 ? '</span>' : '') . '</b> ' . SHOW_COMMUNITY_FORUM_TOTAL;
        echo '</td>';
        echo '<td style="width: 240px; vertical-align: top;">' . niceDate($communityThreadsRow['thread_last_posted_on'], 'D M j, y @ g:ia');
        $communityMessagesRow = (new \Databases\CommunityMessages())->findLatestByThreadId($communityThreadsRow['thread_id']);
        if ($communityMessagesRow) {
            echo '<br />' . SHOW_COMMUNITY_FORUM_BY . ': ' . getUsername($communityMessagesRow['message_user_id'], 1);
        }
        echo '</td>';
        echo '</tr></table>';
        echo '</div>';
        echo boxInsideBottom();
    }
    echo boxOutsideBottom();
} else {
    echo boxOutsideTop(SHOW_COMMUNITY_FORUM_MESSAGES);
    echo boxInsideTop();
    echo SHOW_COMMUNITY_FORUM_ERROR_NO_THREADS_YET;
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if (count($communityThreadsRows) > 5) {
    echo makePages('<a href="?s=communities">Communities</a> - <a href="?s=community&i=' . $communityForumsRow['community_id'] . '">' . $communityForumsRow['community_name'] . '</a> - ' . $communityForumsRow['forum_name_' . language()] . '<br />' . $pages);
}

echo boxOutsideTop(SHOW_COMMUNITY_FORUM_POST_NEW_MESSAGE);
echo boxInsideTop();
if ($GLOBALS['auth']['id'] && ($communityForumsRow['forum_locked'] == 0 || $GLOBALS['auth']['community']['forum_lock_post']) && ($communityForumsRow['forum_automated'] == 0 || $GLOBALS['auth']['community']['forum_automate_post'])) {
?>
    <form action="?a=community_thread_add&i=<?php echo $communityForumsRow['forum_id']; ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <label for="forum_title"><b><?php echo SHOW_COMMUNITY_FORUM_SUBJECT ?></b></label>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="text" name="title" id="forum_title" maxlength="<?php echo COMMUNITY_THREAD_TITLE_MAX; ?>" class="input_text" style="width: 90%;"><br />
        <div style="padding: 5px 0px 0px 0px;"></div>
        <label for="forum_body"><b><?php echo SHOW_COMMUNITY_FORUM_BODY ?></b></label>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <textarea name="body" id="forum_body" class="input_text" style="height: 120px; width: 90%;"></textarea>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="<?php echo SHOW_COMMUNITY_FORUM_POST_NEW_MESSAGE ?>" class="input_submit">
    </form>
<?php
} elseif ($GLOBALS['auth']['id']) {
    if ($communityForumsRow['forum_locked']) {
        echo SHOW_COMMUNITY_FORUM_LOCKED;
    } elseif ($communityForumsRow['forum_automated']) {
        echo SHOW_COMMUNITY_FORUM_AUTOMATED;
    }
} else {
    echo SHOW_COMMUNITY_FORUM_MUST_BE_LOGGED_IN;
}
echo boxInsideBottom();
echo boxOutsideBottom();

if ($GLOBALS['auth']['community']['forum_lock']) {
    echo boxOutsideTop(SHOW_COMMUNITY_FORUM_MODERATOR_TOOLS);
    echo boxInsideTop();
    if ($communityForumsRow['forum_locked']) {
        echo makePostLink('?a=community_forum_lock&i=' . $communityForumsRow['forum_id'] . '&unlock=1', SHOW_COMMUNITY_FORUM_UNLOCK_THIS_FORUM, 'Are you sure you want to unlock this forum?\r\n Any member will be able to post again in it.') . '<br />';
    } else {
        echo makePostLink('?a=community_forum_lock&i=' . $communityForumsRow['forum_id'] . '&lock=1', SHOW_COMMUNITY_FORUM_LOCK_THIS_FORUM, 'Are you sure you want to lock this forum?\r\n No further posting will be allowed (except by authorised moderators).') . '<br />';
    }
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

include('../include/parts/footer.php');

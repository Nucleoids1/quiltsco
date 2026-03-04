<?php
require_once('../include/functions/community_banned.php');
require_once('../include/functions/community_permissions.php');
require_once('../include/functions/community_thread_overall_rating.php');
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'forum';


$_id = get('i');
$_type = get('type', 'new');

if ($_type != 'new' && $_type != 'my_new') {
    $_type = 'new';
}

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
}

$communityRow = (new \Databases\Community())->findById($_id);
if (!$communityRow) {
    header('Location: ./?s=communities');
    die;
}

communityBanned($communityRow['community_id']);
communityPermissions($communityRow['community_id']);

include('../include/parts/header.php');

$j = 0;
$communitySectionsRows = (new \Databases\CommunitySections())->selectActiveByCommunity($communityRow['community_id']);
foreach ($communitySectionsRows as $communitySectionsRow) {
    $communityForumsRows = (new \Databases\CommunityForums())->selectActiveBySection($communitySectionsRow['section_id']);
    foreach ($communityForumsRows as $communityForumsRow) {
        $communityThreadsRows = (new \Databases\CommunityThreads())->selectRecentByForum($communityForumsRow['forum_id'], COMMUNITY_THREADS_PER_PAGE);
        if ($communityThreadsRows) {
            $i = 0;
            $stickiesHeader = 0;
            $normalHeader = 0;
            foreach ($communityThreadsRows as $communityThreadsRow) {
                $newReplyCount = 0;
                $newThread = 0;
                $communityThreadsPointersRow = (new \Databases\CommunityThreadsPointers())->findByUserAndThread($GLOBALS['auth']['id'], $communityThreadsRow['thread_id']);
                if ($communityThreadsPointersRow) {
                    $newReplyCount = (new \Databases\CommunityMessages())->countByThreadIdAfterMessage($communityThreadsRow['thread_id'], $communityThreadsPointersRow['message_id']);
                } else {
                    $newReplyCount = $communityThreadsRow['thread_messages'];
                }
                if ($newReplyCount) {
                    $newThread = 1;
                }
                if ($communityThreadsRow['thread_user_id'] == $GLOBALS['auth']['id']) {
                    $myReplyCount['num'] = 1;
                } else {
                    $myReplyCount['num'] = (new \Databases\CommunityMessages())->countByUserAndThreadId($GLOBALS['auth']['id'], $communityThreadsRow['thread_id']);
                }
                if (($_type == 'new' && $newThread) || ($_type == 'my_new' && $newThread && $myReplyCount['num'])) {
                    if ($i == 0) {
                        echo boxOutsideTop('<a href="?s=community">Communities</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . $communityRow['community_name'] . '</a> - ' . $communityForumsRow['forum_name_' . language()], '[ ' . ($_type == 'my_new' ? '<a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=new">New Threads</a>' : 'New Threads') . ' | ' . ($_type == 'new' ? '<a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=my_new">Participating New Threads</a>' : 'Participating New Threads') . ' | ' . makePostLink('?a=community_mark_forum&i=' . $communityForumsRow['forum_id'], 'Mark All As Viewed') . ' ]');
                    }
                    if ($i++) {
                        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                    }
                    echo boxInsideTop();
                    $status = '';
                    if ($communityThreadsRow['thread_locked']) {
                        $status .= ' <span class="notice_attention">locked</span>';
                    }
                    echo '<div style="cursor: pointer;" role="link" tabindex="0" onClick="document.location=\'?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l\';" onKeyDown="if(event.key===\'Enter\'||event.key===\' \'||event.code===\'Space\'){event.preventDefault();document.location=\'?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l\';}">';
                    echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
                    if ($newThread) {
                        echo '<td class="notice_attention" style="width: 30px; font-weight: bold; text-align: center;">' . SHOW_COMMUNITY_FORUM_NEW . '</td>';
                    }
                    if ($communityThreadsRow['thread_sticky']) {
                        echo '<td class="notice_attention" style="width: 30px; font-weight: bold; text-align: center;">STICK</td>';
                    }
                    echo '<td style="padding-left: 8px;"><div style="font-weight: bold; font-size: 1.0rem; display: inline;"' . ($myReplyCount['num'] ? ' class="notice_good"' : ' class="notice_error"') . '">&raquo;</div> <div style="font-weight: bold; display: inline;">' . makeLink('?s=community_thread&i=' . $communityThreadsRow['thread_id'] . '#l', htmlentities($communityThreadsRow['thread_title'])) . ($status ? $status : '') . '</div><div>' . SHOW_COMMUNITY_FORUM_BY . ': ' . getUsername($communityThreadsRow['thread_user_id'], 1) . '</div></td>';
                    echo '<td style="width: 50px; padding-right: 5px; text-align: right; vertical-align: top;">' . SHOW_COMMUNITY_FORUM_VIEWS . ':</td>';
                    echo '<td style="width: 100px; vertical-align: top;"><b>' . ($communityThreadsRow['thread_views'] > 1000 ? '<span class="notice_error">' : '') . number_format($communityThreadsRow['thread_views']) . ($communityThreadsRow['thread_views'] > 1000 ? '</span>' : '') . '</b><br />' . threadOverallRating($communityThreadsRow['thread_id']) . '</td>';
                    echo '<td style="width: 50px; padding-right: 5px; text-align: right; vertical-align: top;">' . SHOW_COMMUNITY_FORUM_REPLIES . ':</td>';
                    echo '<td style="width: 90px; vertical-align: top;">';
                    if ($GLOBALS['auth']['id']) {
                        if ($newReplyCount) {
                            echo '<span class="notice_attention"><b>' . number_format($newReplyCount) . '</b> ' . SHOW_COMMUNITY_FORUM_NEW . '</span>';
                        } else {
                            echo '<b>' . number_format($newReplyCount) . '</b> ' . SHOW_COMMUNITY_FORUM_NEW;
                        }
                        echo '<br />';
                    }
                    echo '<b>' . ($communityThreadsRow['thread_messages'] > 100 ? '<span class="notice_attention">' : '') . number_format($communityThreadsRow['thread_messages']) . ($communityThreadsRow['thread_messages'] > 100 ? '</span>' : '') . '</b> ' . SHOW_COMMUNITY_FORUM_TOTAL;
                    echo '</td>';
                    echo '<td style="width: 180px; vertical-align: top;">' . niceDate($communityThreadsRow['thread_last_posted_on'], 'D M j, y @ g:ia');
                    $communityMessagesRow = (new \Databases\CommunityMessages())->findLatestByThreadId($communityThreadsRow['thread_id']);
                    if ($communityMessagesRow) {
                        echo '<br />' . SHOW_COMMUNITY_FORUM_BY . ': ' . getUsername($communityMessagesRow['message_user_id'], 1);
                    }
                    echo '</td>';
                    echo '</tr></table>';
                    echo '</div>';
                    echo boxInsideBottom();
                    $j++;
                }
            }
            if ($i) {
                echo boxOutsideBottom();
            }
        }
    }
}
if ($j == 0) {
    echo boxOutsideTop('<a href="?s=community">Communities</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . $communityRow['community_name'] . '</a> - My Forums', '[ <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=new">All New Messages</a> | <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '&type=my_new">Participating New Messages</a> ]');
    echo boxInsideTop();
    echo 'There are no threads matching what you want to view...';
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

include('../include/parts/footer.php');

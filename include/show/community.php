<?php
require_once('../include/functions/community_banned.php');

$GLOBALS['highlight'] = 'forum';

if (language() == 'french') {
    define('SHOW_COMMUNITY_COMMUNITY', 'Les Communautés');
    define('SHOW_COMMUNITY_THREADS', 'Threads');
    define('SHOW_COMMUNITY_MESSAGES', 'Messages');
    define('SHOW_COMMUNITY_NEW', 'nouveau');
    define('SHOW_COMMUNITY_TOTAL', 'totaux');
    define('SHOW_COMMUNITY_HELP', 'Aide');
    define('SHOW_COMMUNITY_MARK_ALL_AS_VIEWED', 'Marquer Tout Comme Regardé');
} else {
    define('SHOW_COMMUNITY_COMMUNITY', 'Communities');
    define('SHOW_COMMUNITY_THREADS', 'Threads');
    define('SHOW_COMMUNITY_MESSAGES', 'Messages');
    define('SHOW_COMMUNITY_NEW', 'new');
    define('SHOW_COMMUNITY_TOTAL', 'total');
    define('SHOW_COMMUNITY_HELP', 'Help');
    define('SHOW_COMMUNITY_MARK_ALL_AS_VIEWED', 'Mark All As Viewed');
}

$_id = getInt('i');

$communityRow = (new \Databases\Community())->findById($_id);
if (!$communityRow) {
    header('Location: ./?s=communities');
    die;
}

communityBanned($communityRow['community_id']);

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=communities">' . SHOW_COMMUNITY_COMMUNITY . '</a> - ' . htmlentities($communityRow['community_name']), '[ <a href="?s=community_help">' . SHOW_COMMUNITY_HELP . '</a>' . ($GLOBALS['auth']['id'] ? ' | <a href="?s=community_my_forum&i=' . $communityRow['community_id'] . '">My Forums</a> | ' . makePostLink('?a=community_mark_community&i=' . $communityRow['community_id'], SHOW_COMMUNITY_MARK_ALL_AS_VIEWED) : '') . ' ]');
echo boxInsideTop();
echo 'Welcome to the <b>' . htmlentities($communityRow['community_name']) . '</b> community.';
echo boxInsideBottom();
echo boxOutsideBottom();

$i = 0;
$communitySectionsRows = (new \Databases\CommunitySections())->selectActiveByCommunityOrdered($communityRow['community_id']);
foreach ($communitySectionsRows as $communitySectionsRow) {
    echo boxOutsideTop(htmlentities($communitySectionsRow['section_name_' . language()]));
    $j = 0;
    $communityForumsRows = (new \Databases\CommunityForums())->selectActiveBySectionOrdered($communitySectionsRow['section_id']);
    foreach ($communityForumsRows as $communityForumsRow) {
        if ($j++ > 0) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo boxInsideTop();
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        echo '<td>';
        echo '<span style="font-size: 1.05rem; font-weight: bold;">' . makeLink('?s=community_forum&i=' . $communityForumsRow['forum_id'], htmlentities($communityForumsRow['forum_name_' . language()])) . ($communityForumsRow['forum_locked'] ? ' <span class="notice_attention">Locked</span>' : '') . '</span><br />';
        echo '<span style="font-size: 1.0rem;">' . htmlentities($communityForumsRow['forum_description_' . language()]) . '</span>';
        echo '</td>';
        echo '<td style="width: 100px; padding-right: 5px; text-align: right;">' . SHOW_COMMUNITY_THREADS . ':<br />' . SHOW_COMMUNITY_MESSAGES . ':</td>';
        echo '<td style="width: 110px;">';
        echo '<b>' . $communityForumsRow['forum_threads'] . '</b> ' . SHOW_COMMUNITY_TOTAL . '<br />';
        echo '<b>' . $communityForumsRow['forum_messages'] . '</b> ' . SHOW_COMMUNITY_TOTAL;
        echo '</td>';
        echo '<td style="width: 110px;">';
        if ($GLOBALS['auth']['id']) {
            echo '<br />';
            $newMessages = getNewMessages($communityForumsRow['forum_id']);
            echo ($newMessages ? '<div class="notice_attention">' : '');
            echo '<b>' . $newMessages . '</b> ' . SHOW_COMMUNITY_NEW;
            echo ($newMessages ? '</div>' : '');
        } else {
            echo '<b>0</b> ' . SHOW_COMMUNITY_NEW . '<br />';
        }
        echo '</td>';
        echo '</tr></table>';
        echo boxInsideBottom();
    }
    echo boxOutsideBottom();
}

echo boxOutsideTop('Moderators');
echo boxInsideTop();
$arr = array();
$communityPermissionsRows = (new \Databases\CommunityPermissions())->selectDistinctUserIdsByCommunity($communityRow['community_id']);
foreach ($communityPermissionsRows as $communityPermissionsRow) {
    $username = getUsername($communityPermissionsRow['user_id']);
    $arr[] = makeLink('?s=profile&u=' . $username, $username);
}
echo implode(', ', $arr);
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

function getNewMessages($forumId)
{
    $new = 0;
    $communityThreadsRows = (new \Databases\CommunityThreads())->selectRecentByForum($forumId, COMMUNITY_THREADS_PER_PAGE * 2);
    foreach ($communityThreadsRows as $communityThreadsRow) {
        $communityThreadsPointersRow = (new \Databases\CommunityThreadsPointers())->findByUserAndThread($GLOBALS['auth']['id'], $communityThreadsRow['thread_id']);
        if ($communityThreadsPointersRow) {
            $new = $new + (new \Databases\CommunityMessages())->countByThreadIdBetweenMessages($communityThreadsRow['thread_id'], $communityThreadsPointersRow['message_id'], $communityThreadsRow['thread_last_message_id']);
        } else {
            $new = $new + $communityThreadsRow['thread_messages'];
        }
    }
    return $new;
}

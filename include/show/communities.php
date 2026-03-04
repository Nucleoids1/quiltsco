<?php
$GLOBALS['highlight'] = 'forum';

if (language() == 'french') {
    define('SHOW_COMMUNITY_COMMUNITY', 'Les Communautés');
    define('SHOW_COMMUNITY_NEW', 'nouveau');
    define('SHOW_COMMUNITY_TOTAL', 'total');
    define('SHOW_COMMUNITY_THREADS', 'Threads');
    define('SHOW_COMMUNITY_MESSAGES', 'Messages');
} else {
    define('SHOW_COMMUNITY_COMMUNITY', 'Communities');
    define('SHOW_COMMUNITY_NEW', 'new');
    define('SHOW_COMMUNITY_TOTAL', 'total');
    define('SHOW_COMMUNITY_THREADS', 'Threads');
    define('SHOW_COMMUNITY_MESSAGES', 'Messages');
}

include('../include/parts/header.php');

echo boxOutsideTop(SHOW_COMMUNITY_COMMUNITY);
$i = 0;
$communityRows = (new \Databases\Community())->selectAllOrderedById();
foreach ($communityRows as $communityRow) {
    $communityForumsRow = (new \Databases\CommunityForums())->sumForumStatsByCommunity($communityRow['community_id']);
    if ($i++) {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    }
    echo boxInsideTop();
    echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
    echo '<td style="font-size: 1.05rem; font-weight: bold; text-align: left;">' . makeLink('?s=community&i=' . $communityRow['community_id'], safeAttr($communityRow['community_name'])) . '</td>';
    echo '<td style="width: 100px; padding-right: 5px; text-align: right;">' . SHOW_COMMUNITY_THREADS . ':<br />' . SHOW_COMMUNITY_MESSAGES . ':</td>';
    echo '<td style="width: 100px;">';
    echo '<b>' . $communityForumsRow['forum_threads'] . '</b> ' . SHOW_COMMUNITY_TOTAL . '<br />';
    echo '<b>' . $communityForumsRow['forum_messages'] . '</b> ' . SHOW_COMMUNITY_TOTAL;
    echo '</td>';
    echo '</tr></table>';
    echo boxInsideBottom();
}
echo boxOutsideBottom();

include('../include/parts/footer.php');

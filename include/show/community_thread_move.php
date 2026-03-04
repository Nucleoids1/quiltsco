<?php
require_once('../include/functions/community_banned.php');
require_once('../include/functions/community_permissions.php');

$GLOBALS['highlight'] = 'forum';

$_back = decodeUrlPath(get('b'), 's=community_thread&i=' . getInt('i'));
$_backOld = encodeUrlPath($_back);
$_id = getInt('i');

$communityRow = (new \Databases\CommunityThreads())->findThreadContextForDisplay($_id, language());
if (!$communityRow) {
    makeCookie('notice', 'This thread does not exists.');
    header('Location: ./?s=communities');
    die;
}

communityBanned($communityRow['community_id']);
communityPermissions($communityRow['community_id'], $communityRow['section_id'], $communityRow['forum_id']);

if (!$GLOBALS['auth']['community']['thread_move']) {
    makeCookie('notice', 'You do not have permission to move this thread.');
    header('Location: ./?s=community_thread&i=' . $communityRow['thread_id']);
    die;
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community">Communities</a> - <a href="?s=community&i=' . $communityRow['community_id'] . '">' . safeAttr($communityRow['community_name']) . '</a> - <a href="?s=community_forum&i=' . $communityRow['forum_id'] . '">' . safeAttr($communityRow['forum_name_' . language()]) . '</a> - Move Thread');
echo boxInsideTop();
echo 'To where do you want to move this message?';
echo boxInsideBottom();
echo boxOutsideBottom();

$i = 0;
$communitySectionsRows = (new \Databases\CommunitySections())->selectActiveByCommunityOrdered($communityRow['community_id']);
foreach ($communitySectionsRows as $communitySectionsRow) {
    echo boxOutsideTop($communitySectionsRow['section_name_' . language()]);
    $j = 0;
    $communityForumsRows = (new \Databases\CommunityForums())->selectActiveBySectionOrdered($communitySectionsRow['section_id']);
    foreach ($communityForumsRows as $communityForumsRow) {
        if ($j++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo boxInsideTop();
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        echo '<td>';
        echo '<div style="font-size: 1.05rem;"><span style="font-weight: bold;">' . makeLink(safeUrl('?a=community_thread_move&i=' . $communityRow['thread_id'] . '&x=' . $communityForumsRow['forum_id'] . '&b=' . $_backOld), safeAttr($communityForumsRow['forum_name_' . language()])) . '</span>' . ($communityForumsRow['forum_locked'] ? ' LOCKED' : '') . '</div>';
        echo '<div style="font-size: 1.0rem;">' . safeAttr($communityForumsRow['forum_description_' . language()]) . '</div>';
        echo '</td>';
        echo '</tr></table>';
        echo boxInsideBottom();
    }
    echo boxOutsideBottom();
}

include('../include/parts/footer.php');

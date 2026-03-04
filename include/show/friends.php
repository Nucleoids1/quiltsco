<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'friends';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$entries['num'] = (new \Databases\Friends())->countAll();
$pageCount = ceil($entries['num'] / 100);
$pages = '<span class="pages">Page: ' . pages('?s=friends', $_page, $pageCount) . '</span>';
?>

<h1 class="header">Friends</h1>
<div class="content" style="padding: 0px 5px 5px 5px;">
    <?php
    $friendsRows = (new \Databases\Friends())->findByUserIdPage($GLOBALS['auth']['id'], 100, ($_page - 1) * 100);
    if ($friendsRows) {
        $thumbWidth = intval(THUMB_WIDTH / 1.3);
        $thumbHeight = intval(THUMB_HEIGHT / 1.3);
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td>';
        foreach ($friendsRows as $friendsRow) {
            echo boxImageTop();
            echo '<table style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="text-align: center; vertical-align: top;">';
            echo '<a href="?s=profile&u=' . getUsername($friendsRow['friend_id']) . '"><img src="?g=thumb&i=' . getMainImageId($friendsRow['friend_id']) . '" alt="Profile photo of ' . safeAttr(getUsername($friendsRow['friend_id'])) . '" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: 0px;"></a><br />';
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            echo getUsername($friendsRow['friend_id']) . '<br />';
            echo '</td></tr></table>';
            echo boxImageBottom();
        }
        echo '</td></tr></table>';
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>You don\'t have any friends...';
    }
    ?>
</div>

<?php
include('../include/parts/footer.php');

<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'members';


$_display = get('d');
$_page = getInt('p', 1);

include('../include/parts/header.php');

if (!$_display) {
    $_display = 'all';
} elseif (!(ord($_display) >= 65 && ord($_display) <= 90) && !(ord($_display) >= 97 && ord($_display) <= 122)) {
    $_display = 'all';
}
if ($_display == 'all') {
    $imageCount = (new \Databases\Members())->countAll();
} else {
    $imageCount = (new \Databases\Members())->countByUsernamePrefix($_display);
}
$pageCount = ceil($imageCount / 100);
$pages = '<span class="pages">Page: ' . pages('?s=members' . ($_display ? '&d=' . $_display : ''), $_page, $pageCount) . '</span>';
?>

<h1 class="header">Members<?php echo $imageCount ? '<br />' . $pages : '' ?></h1>
<div class="content">
    <h2 class="header" style="margin-bottom: 0px;">
        <?php
        echo makeLink('?s=members', 'All') . ' : ';
        for ($i = 65; $i <= 90; $i++) {
            echo makeLink('?s=members&d=' . strtolower(chr($i)), chr($i)) . ' ';
        }
        ?>
    </h2>
    <?php
    if ($_display == 'all') {
        $membersRows = (new \Databases\Members())->selectPageByUsername((($_page - 1) * 100), 100);
    } else {
        $membersRows = (new \Databases\Members())->selectPageByUsernamePrefix($_display, (($_page - 1) * 100), 100);
    }
    if ($membersRows) {
        $thumbWidth = intval(THUMB_WIDTH / 1);
        $thumbHeight = intval(THUMB_HEIGHT / 1);
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td>';
        foreach ($membersRows as $membersRow) {
            $userInfoRow = (new \Databases\MembersExtras())->findByUserId($membersRow['id']);
            $username = getUsername($membersRow['id']);
            echo boxImageTop();
            echo '<table style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="text-align: center; vertical-align: top;">';
            echo '<a href="?s=profile&u=' . $username . '"><img src="?g=thumb&i=' . $membersRow['main_image_id'] . '" alt="Profile photo of ' . safeAttr($username) . '" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: solid 0px black;"></a><br />';
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            echo $username;
            echo '</td></tr></table>';
            echo boxImageBottom();
        }
        echo '</td></tr></table>';
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>No members to show...';
    }
    ?>
</div>
<?php
if ($imageCount) {
    echo '<div class="header">' . $pages  . '</div>';
}

include('../include/parts/footer.php');

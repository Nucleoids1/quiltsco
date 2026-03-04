<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'graphix';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$imagesCount = (new \Databases\Images())->countAll();
$pageCount = ceil($imagesCount / IMAGES_PER_PAGE);
$pages = '<span class="pages">Page: ' . pages('?s=graphix', $_page, $pageCount) . '</span>';
?>

<h1 class="header">
    <table role="presentation" style="width: 100%; border-spacing: 0;">
        <tr>
            <td style="vertical-align: top;">
                Graphix<?php echo ($imagesCount) ? '<br />' . $pages : '' ?>
                <?php
                if ($GLOBALS['auth']['id']) {
                    echo '</td><td style="text-align: right; vertical-align: top; color: #cccccc; font-weight: normal;">[ ' . makeLink('?s=upload_image', 'Upload Graphix') . ' ]';
                }
                ?>
            </td>
        </tr>
    </table>
</h1>

<div class="content" style="padding: 0px 5px 5px 5px;">
    <?php
    $imagesRows = (new \Databases\Images())->findPageOrderedByPostedOn(IMAGES_PER_PAGE, ($_page - 1) * IMAGES_PER_PAGE);
    if ($imagesRows) {
        echo '<table role="presentation" style="width: 100%; border-spacing: 0;"><tr><td>';
        foreach ($imagesRows as $imagesRow) {
            $imagesCommentsCount = (new \Databases\Comments('images_comments'))->countByLinkId($imagesRow['id']);
            echo boxImageTop();
            echo '<table role="presentation" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border-spacing: 0;"><tr><td style="padding: 0; text-align: center; vertical-align: top;">';
            echo '<a href="?s=image&i=' . $imagesRow['id'] . '"><img src="?g=thumb&i=' . $imagesRow['id'] . '" alt="Gallery image by ' . safeAttr(getUsername($imagesRow['user_id'])) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a><br />';
            echo '<div style="padding: 4px 0px 0px 0px;"></div>';
            echo '<b>' . $imagesCommentsCount . '</b> Comments<br />';
            echo '</td></tr></table>';
            echo boxImageBottom();
        }
        echo '</td></tr></table>';
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;">There are no images in the database.';
    }
    ?>
</div>
<?php
if ($imagesCount) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

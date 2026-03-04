<?php
require_once('../include/functions/comments.php');
require_once('../include/functions/image_overall_rating.php');
require_once('../include/functions/image_overall_type.php');
require_once('../include/functions/image_voting.php');

$GLOBALS['highlight'] = 'graphix';


$_id = getInt('i');

$imagesRow = (new \Databases\Images())->findById($_id);
if (!$imagesRow) {
    echo ('Sorry, that image could not be found.');
    die;
}

include('../include/parts/header.php');
?>

<h1 class="header">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td><?php echo makeLink('?s=graphix', 'Graphix') ?> - Image [ <?php echo $_id ?> ]</td>
            <td style="text-align: right;">Uploaded By <?php echo getUsername($imagesRow['user_id'], 1) ?> On <?php echo niceDate($imagesRow['posted_on']) ?></td>
        </tr>
    </table>
</h1>

<div class="content" style="padding: 0px 0px 5px 5px;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; white-space: nowrap;">
                <?php
                $previous = '';
                $thumbWidth = intval(THUMB_WIDTH / 1.1428571429);
                $thumbHeight = intval(THUMB_HEIGHT / 1.1428571429);
                $userImagesPrevRows = (new \Databases\Images())->findThreeBefore($_id);
                for ($i = 1; $i <= 3; $i++) {
                    $userImagesPrevId = isset($userImagesPrevRows[$i - 1]) ? $userImagesPrevRows[$i - 1] : null;
                    $previous = boxImageTop('', $userImagesPrevId ? 1 : 0) . ($userImagesPrevId ? '<a href="?s=image&i=' . $userImagesPrevId . '"><img src="?g=thumb&i=' . $userImagesPrevId . '" alt="Gallery image thumbnail" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: 0px;"></a>' : '<img src="?g=thumb&i=0" alt="Gallery image placeholder" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: 0px;">') . boxImageBottom() . $previous;
                }
                echo $previous;

                echo '</td><td style="vertical-align: top; white-space: nowrap;">' . "\r\n";

                $next = '';
                $userImagesNextRows = (new \Databases\Images())->findThreeAfter($_id);
                for ($i = 1; $i <= 3; $i++) {
                    $userImagesNextId = isset($userImagesNextRows[$i - 1]) ? $userImagesNextRows[$i - 1] : null;
                    $next = boxImageTop('float: right;', $userImagesNextId ? 1 : 0) . ($userImagesNextId ? '<a href="?s=image&i=' . $userImagesNextId . '"><img src="?g=thumb&i=' . $userImagesNextId . '" alt="Gallery image thumbnail" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: 0px;"></a>' : '<img src="?g=thumb&i=0" alt="Gallery image placeholder" style="width: ' . $thumbWidth . 'px; height: ' . $thumbHeight . 'px; border: 0px;">') . boxImageBottom() . $next;
                }
                echo $next;
                ?>
            </td>
        </tr>
    </table>
</div>

<?php
echo '<div class="header">';
imageVoting($_id);
echo '</div>';

echo '<div class="content">';
echo '<div style="text-align: center;"><img src="?g=full&i=' . $_id . '" alt="Gallery image by ' . safeAttr(getUsername($imagesRow['user_id'])) . '" style="width: ' . $imagesRow['width'] . 'px; height: ' . $imagesRow['height'] . 'px;"></div>' . "\r\n";
echo '</div>';

if ($GLOBALS['auth']['root']) {
    echo '<div class="header">';
    echo 'Administration: ' . makePostLink('?a=image_delete&i=' . $_id . '&b=' . encodeUrlPath(server('QUERY_STRING')), 'Delete Image', 'Delete image?') . ' | ' . makePostLink('?a=image_rotate&i=' . $_id . '&degrees=270&b=' . encodeUrlPath(server('QUERY_STRING')), 'Rotate Left', 'Rotate image left...') . ' | ' . makePostLink('?a=image_rotate&i=' . $_id . '&degrees=180&b=' . encodeUrlPath(server('QUERY_STRING')), 'Flip', 'Flip image...') . ' | ' . makePostLink('?a=image_rotate&i=' . $_id . '&degrees=90&b=' . encodeUrlPath(server('QUERY_STRING')), 'Rotate Right', 'Rotate image right...');
    echo '</div>';
}

comments($_id, 'images_comments');

include('../include/parts/footer.php');

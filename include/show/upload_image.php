<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'upload_image';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$entries['num'] = (new \Databases\GalleryImages())->countByUserId($GLOBALS['auth']['id']);
$pageCount = ceil($entries['num'] / IMAGES_PER_PAGE);
$pages = 'Page: ' . pages('?s=image_upload', $_page, $pageCount) . '<br />';
?>

<h1 class="header">Graphix Image Upload</h1>

<div class="content">
    <?php
    if ($_notice) {
        echo '<span style="color: red;">' . $_notice . '</span><br />';
        echo '<div style="padding: 10px 0px 0px 0px;"></div>';
    }
    ?>
    Upload a new image. <?php echo (MAX_PICTURE_UPLOAD_SIZE / 1000000) ?>MB maximum filesize.<br />
    Accepted formats are: JPEG, GIF, PNG.<br /><br />

    <form action="?a=upload_image" method="post" enctype="multipart/form-data" style="margin: 0px;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="upload_picture">File:</label></td>
                <td><input type="file" name="picture" id="upload_picture" class="input_text"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Upload Image" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<h2 class="header">My Gallery Images<?php echo $entries['num'] ? '<br />' . $pages : '' ?></h2>

<div class="content" style="padding: 0px 5px 5px 5px;">
    <?php
    $mainImageRow = (new \Databases\Members())->findById($GLOBALS['auth']['id']);
    $galleryImagesRows = (new \Databases\GalleryImages())->findPageByUserIdPostedOn($GLOBALS['auth']['id'], IMAGES_PER_PAGE, ($_page - 1) * IMAGES_PER_PAGE);
    if ($galleryImagesRows) {
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td>';
        foreach ($galleryImagesRows as $galleryImagesRow) {
            echo boxImageTop();
            echo '<table style="width: 300px; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="text-align: center; vertical-align: top;">';
            echo '<a href="?s=image&i=' . $galleryImagesRow['image_id'] . '"><img src="?g=thumb&i=' . $galleryImagesRow['image_id'] . '" alt="Gallery image thumbnail" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: 0px;"></a>';
            echo '</td><td style="width: 170px; padding-left: 5px; vertical-align: top;">';
            if ($mainImageRow['main_image_id'] == $galleryImagesRow['image_id']) {
                echo '<div style="border: solid 1px orange; background: black; color: orange; padding: 2px 2px 2px 2px; text-align: center; margin-bottom: 4px;">';
                echo makePostLink('?a=image_remove_main&p=' . $_page, 'Unset Main');
                echo '</div>';
            } else {
                echo '<div style="border: solid 1px green; background: black; color: green; padding: 2px 2px 2px 2px; text-align: center; margin-bottom: 4px;">';
                echo makePostLink('?a=image_set_main&i=' . $galleryImagesRow['image_id'] . '&p=' . $_page, 'Make Main', '', 'background: none; border: 0; padding: 0; margin: 0; color: green; text-decoration: underline; cursor: pointer; font: inherit;');
                echo '</div>';
            }
            if ($mainImageRow['main_image_id'] != $galleryImagesRow['image_id']) {
                echo '<div style="border: solid 1px red; background: black; color: red; padding: 2px 2px 2px 2px; text-align: center; margin-bottom: 4px;">';
                echo makePostLink('?a=image_remove&i=' . $galleryImagesRow['image_id'] . '&p=' . $_page, 'Delete', 'Delete this image?', 'background: none; border: 0; padding: 0; margin: 0; color: red; text-decoration: underline; cursor: pointer; font: inherit;');
                echo '</div>';
            }
            echo '<div style="border: solid 1px grey; background: black; color: grey; padding: 2px 2px 2px 2px; text-align: center;">';
            echo '[' . $galleryImagesRow['image_id'] . ']';
            echo '</div>';
            echo '</td></tr></table> ';
            echo boxImageBottom();
        }
        echo '</td></tr></table> ';
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>You have no graphix in the database.';
    }
    ?>
</div>

<?php
if ($entries['num']) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

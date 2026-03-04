<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'tiles_pending';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$entries['num'] = (new \Databases\TilesPending())->countByUserId($GLOBALS['auth']['id']);
$pageCount = ceil($entries['num'] / 1);
$pages = '<span class="pages">Page: ' . pages('?s=tiles_pending', $_page, $pageCount) . '</span><br />';
?>

<h1 class="header">Pending Tiles<?php echo $entries['num'] ? '<br />' . $pages : '' ?></h1>

<div class="content" style="padding: 0px 5px 5px 5px;">
    <?php
    $tilesPendingRow = (new \Databases\TilesPending())->findByUserIdPage($GLOBALS['auth']['id'], ($_page - 1) * 1);
    if ($tilesPendingRow) {
        $quiltsRow = (new \Databases\Quilts())->findById($tilesPendingRow['quilt_id']);
        if ($quiltsRow) {
            echo '<table role="presentation" style="padding-top: 3px; width: 100%; border-collapse: collapse; border-spacing: 0;"><tr><td style="width: 400px; vertical-align: top;">';
            echo '<img src="?g=tile_sides&i=' . $tilesPendingRow['quilt_id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y'] . '&borders=' . $tilesPendingRow['borders'] . '" alt="Pending quilt tile preview" style="width: ' . ($quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2) . 'px; height: ' . ($quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2) . 'px;"><br />';
            echo '<span style="font-size: 0.3125rem;"><br /></span>';
            echo '<form action="?a=tile_cancel&i=' . $tilesPendingRow['quilt_id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y'] . '" method="post" style="margin: 0px;">';
            echo csrfField();
            echo '<input type="submit" value="Cancel Tile" class="input_submit">';
            echo '</form>';
            echo '</td><td style="vertical-align: top;">';
            echo '<form action="?a=upload_tile&i=' . $tilesPendingRow['quilt_id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y'] . '" method="post" enctype="multipart/form-data" style="margin: 0px;">';
            echo csrfField();
            echo '<table style="border-collapse: collapse; border-spacing: 0;" role="presentation">';
            echo '<tr><td class="form_label_cell">From Quilt:</td><td class="form_input_cell">' . makeLink('?s=quilt&i=' . $tilesPendingRow['quilt_id'], $quiltsRow['name']) . '</td></tr>';
            echo '<tr><td class="form_label_cell">Checkout Date:</td><td class="form_input_cell">' . niceDate($tilesPendingRow['started_on'], 'F j, Y @ g:ia') . '</td></tr>';
            echo '<tr><td class="form_label_cell">Due Date:</td><td class="form_input_cell">' . niceDate($tilesPendingRow['started_on'], 'F j, Y @ g:ia', $quiltsRow['timelimit']) . '</td></tr>';
            echo '<tr><td class="form_label_cell"><label for="pending_tile">Checkin Tile:</label></td><td class="form_input_cell"><input name="tile" id="pending_tile" type="file" class="input_text"></td></tr>';
            echo '<tr><td class="form_label_cell"><label for="pending_comment">Comment:</label></td><td class="form_input_cell"><input name="comment" id="pending_comment" class="input_text" style="width: 320px;"></td></tr>';
            echo '<tr><td class="form_label_cell"></td><td class="form_input_cell"><input type="submit" value="Checkin This Tile" class="input_submit"></td></tr>';
            echo '</table>';
            echo '</form>';
            echo '</td></tr></table>';
        }
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>You do not have any tiles pending.';
    }
    ?>
</div>

<?php
if ($entries['num']) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

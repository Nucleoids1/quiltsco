<?php
require_once('../include/functions/comments.php');
require_once('../include/functions/quilt_information.php');
require_once('../include/functions/tile_is_available.php');

$GLOBALS['highlight'] = 'quilts';


$_id = getInt('i');

$quiltsRow = (new \Databases\Quilts())->findById($_id);
if ($quiltsRow) {
    if ($quiltsRow['finished']) {
        $GLOBALS['highlight'] = 'finished';
    }
} else {
    header('Location: ./?s=quilts');
    die;
}

if (post('quilt_zoom')) {
    makeCookie('quilt_zoom', post('quilt_zoom'));
    header('Location: ./?s=quilt&i=' . $quiltsRow['id']);
    die;
}

include('../include/parts/header.php');

echo '<h1 class="header">';
echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
echo '<td>' . makeLink('?s=' . ($quiltsRow['finished'] ? 'finished' : 'quilts'), 'Quilts') . ' - ' . $quiltsRow['name'] . '</td>';
echo '<td style="text-align: right;">Full: [ ' . makeLink('?g=quilt_full&i=' . $quiltsRow['id'] . '&format=jpg', 'JPG') . ' ] [ ' . makeLink('?g=quilt_full&i=' . $quiltsRow['id'], 'PNG') . ' ] ';
echo '<form name="quilt_zoom" action="?s=quilt&i=' . $quiltsRow['id'] . '" method="post" style="margin: 0px; display: inline;"><label for="quilt_zoom_select">Quilt Zoom:</label> <select class="input_select" name="quilt_zoom" id="quilt_zoom_select" onChange="document.quilt_zoom.submit();">';
echo '<option value="1.2"' . (cookie('quilt_zoom') == 1.2 ? ' selected' : '') . '>120%</option>';
echo '<option value="1.1"' . (cookie('quilt_zoom') == 1.1 ? ' selected' : '') . '>110%</option>';
echo '<option value="1"' . (cookie('quilt_zoom', 1) == 1 ? ' selected' : '') . '>100%</option>';
echo '<option value="0.90"' . (cookie('quilt_zoom') == 0.9 ? ' selected' : '') . '>90%</option>';
echo '<option value="0.80"' . (cookie('quilt_zoom') == 0.8 ? ' selected' : '') . '>80%</option>';
echo '<option value="0.70"' . (cookie('quilt_zoom') == 0.7 ? ' selected' : '') . '>70%</option>';
echo '<option value="0.60"' . (cookie('quilt_zoom') == 0.6 ? ' selected' : '') . '>60%</option>';
echo '<option value="0.50"' . (cookie('quilt_zoom') == 0.5 ? ' selected' : '') . '>50%</option>';
echo '<option value="0.40"' . (cookie('quilt_zoom') == 0.4 ? ' selected' : '') . '>40%</option>';
echo '</select></form></td>';
echo '</tr></table>';
echo '</h1>' . "\r\n";

echo '<div class="content">';
if ($_notice) {
    echo '<span style="color: red;">' . $_notice . '</span><br />';
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
}
quiltInformation($quiltsRow['id']);
$tilesRows = (new \Databases\Tiles())->selectDistinctUserIdsByQuilt($quiltsRow['id']);
if (count($tilesRows)) {
    $array = array();
    echo '<div style="padding: 10px 0px 0px 0px;"></div>' . "\r\n";
    echo 'The following artists worked on this quilt: ';
    foreach ($tilesRows as $tilesRow) {
        $tilesCount = (new \Databases\Tiles())->countByQuiltAndUserNotDeleted($quiltsRow['id'], $tilesRow['user_id']);
        $username = getUsername($tilesRow['user_id']);
        $array[$username] = substr('0000' . $tilesCount, -4) . nameKey(strtolower($username));
    }
    arsort($array);
    $i = 0;
    foreach ($array as $key => $value) {
        if ($i) {
            echo ', ';
        }
        echo makeLink('?s=profile&u=' . $key, $key) . ' (' . intval(substr($value, 0, 4)) . ')';
        $i++;
    }
    echo '<br />';
}

function nameKey($name)
{
    $return = '';
    for ($i = 0; $i < strlen($name); $i++) {
        $return .= '-' . substr('000' . intval(255 - ord(substr($name, $i, 1))), -3);
    }
    return $return;
}

echo '<div style="padding: 10px 0px 0px 0px;"></div>' . "\r\n";
echo '<span style="color: #bfdc92;">' . $quiltsRow['description'] . '</span><br />' . "\r\n";
echo '</div>';

$tilesPendingRow = (new \Databases\TilesPending())->findByQuiltAndUser($quiltsRow['id'], $GLOBALS['auth']['id']);
if ($tilesPendingRow) {
    echo '<div class="header">You are currently working on a tile in this project.</div>' . "\r\n";
    echo '<div class="content">';
    echo '<table style="border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="vertical-align: top;">';
    echo '<img src="' . safeUrl('?g=tile_sides&i=' . $quiltsRow['id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y'] . '&borders=' . $tilesPendingRow['borders']) . '" alt="Pending tile preview" style="width: ' . ($quiltsRow['tile_width'] + ($quiltsRow['side_pixels'] * 2)) . 'px; height: ' . ($quiltsRow['tile_height'] + ($quiltsRow['side_pixels'] * 2)) . 'px;"><br />';
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo '<form action="' . safeUrl('?a=tile_cancel&i=' . $quiltsRow['id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y']) . '" method="post" style="margin: 0px;">';
    echo csrfField();
    echo '<input type="submit" value="Cancel Tile" class="input_submit">';
    echo '</form>';
    echo '</td><td style="vertical-align: top;">';
    echo '<form action="' . safeUrl('?a=upload_tile&i=' . $quiltsRow['id'] . '&x=' . $tilesPendingRow['matrix_x'] . '&y=' . $tilesPendingRow['matrix_y']) . '" method="post" enctype="multipart/form-data" style="margin: 0px;">';
    echo csrfField();
    echo '<table style="border-collapse: collapse; border-spacing: 0;" role="presentation">';
    echo '<tr><td class="form_label_cell">Checkout Date:</td><td class="form_input_cell">' . niceDate($tilesPendingRow['started_on'], 'F j, Y @ g:ia') . '</td></tr>';
    echo '<tr><td class="form_label_cell">Due Date:</td><td class="form_input_cell">' . niceDate($tilesPendingRow['started_on'], 'F j, Y @ g:ia', $quiltsRow['timelimit']) . '</td></tr>';
    echo '<tr><td class="form_label_cell"><label for="quilt_tile">Checkin Tile:</label></td><td class="form_input_cell"><input name="tile" id="quilt_tile" type="file" class="input_text"></td></tr>';
    echo '<tr><td class="form_label_cell"><label for="quilt_comment">Comment:</label></td><td class="form_input_cell"><input name="comment" id="quilt_comment" class="input_text" style="width: 320px;"></td></tr>';
    echo '<tr><td class="form_label_cell"></td><td class="form_input_cell"><input type="submit" value="Checkin This Tile" class="input_submit"></td></tr>';
    echo '</table>';
    echo '</form>';
    echo '</td></tr></table>';
    echo '</div>';
}
?>
</div>
</div>
</div>

<div class="quilt_tiles">
    <table style="text-align: center; border-collapse: collapse; border-spacing: 0;" role="presentation" class="quilt-tiles-grid">
        <?php
        for ($y = 1; $y <= $quiltsRow['quilt_height']; $y++) {
            $zoom = cookie('quilt_zoom', 1);
            echo '<tr>' . "\r\n";
            for ($x = 1; $x <= $quiltsRow['quilt_width']; $x++) {
                list($available, $display, $output) = tileIsAvailable($quiltsRow['id'], $x, $y, $zoom);
                echo $output . "\r\n";
            }
            echo '</tr>' . "\r\n";
        }
        ?>
    </table>
</div>

<div class="major_fill">
    <div class="minor_fill">
        <div class="fixup_fill">
            <?php
            comments($_id, 'quilts_comments');

            include('../include/parts/footer.php');

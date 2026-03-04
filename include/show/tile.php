<?php
require_once('../include/functions/comments.php');
require_once('../include/functions/tile_is_available.php');

$GLOBALS['highlight'] = 'quilts';


$_id = getInt('i');
$_tileZoom = post('tile_zoom');

if ($_tileZoom && $_id) {
    makeCookie('tile_zoom', $_tileZoom);
    header('Location: ./?s=tile&i=' . $_id);
    die;
}

$tilesRow = (new \Databases\Tiles())->findById($_id);
if ($tilesRow) {
    list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y']);
    if ($display) {
        $quiltsRow = (new \Databases\Quilts())->findById($tilesRow['quilt_id']);
        if ($quiltsRow['finished']) {
            $GLOBALS['highlight'] = 'finished';
        }
    } else {
        header('Location: ./?s=quilt&i=' . $tilesRow['quilt_id']);
        die;
    }
} else {
    header('Location: ./?s=finished');
    die;
}

include('../include/parts/header.php');

echo '<h1 class="header">';
echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
echo '<td>' . makeLink('?s=' . ($quiltsRow['finished'] ? 'finished' : 'quilts'), 'Quilts') . ' - ' . makeLink('?s=quilt&i=' . $quiltsRow['id'], $quiltsRow['name']) . ' - View Tile</td>';
echo '<td style="text-align: right;"><form name="tile_zoom" action="?s=tile&i=' . $_id . '" method="post" style="margin: 0px; display: inline;"><label for="tile_zoom_select">Tile Zoom:</label> <select class="input_select" name="tile_zoom" id="tile_zoom_select" onChange="document.tile_zoom.submit();">';
echo '<option value="1.2"' . (cookie('tile_zoom') == 1.2 ? ' selected' : '') . '>120%</option>';
echo '<option value="1.1"' . (cookie('tile_zoom') == 1.1 ? ' selected' : '') . '>110%</option>';
echo '<option value="1"' . (cookie('tile_zoom', 1) == 1 ? ' selected' : '') . '>100%</option>';
echo '<option value="0.90"' . (cookie('tile_zoom') == 0.9 ? ' selected' : '') . '>90%</option>';
echo '<option value="0.80"' . (cookie('tile_zoom') == 0.8 ? ' selected' : '') . '>80%</option>';
echo '<option value="0.70"' . (cookie('tile_zoom') == 0.7 ? ' selected' : '') . '>70%</option>';
echo '<option value="0.60"' . (cookie('tile_zoom') == 0.6 ? ' selected' : '') . '>60%</option>';
echo '<option value="0.50"' . (cookie('tile_zoom') == 0.5 ? ' selected' : '') . '>50%</option>';
echo '<option value="0.40"' . (cookie('tile_zoom') == 0.4 ? ' selected' : '') . '>40%</option>';
echo '</select></form></td>';
echo '</tr></table>';
echo '</h1>';

echo '<div class="content">';
$quiltsPermissionsRows = (new \Databases\QuiltsPermissions())->selectByUserAndQuilt($GLOBALS['auth']['id'], $tilesRow['quilt_id']);
if (count($quiltsPermissionsRows) || $tilesRow['user_id'] == $GLOBALS['auth']['id']) {
    echo '<div class="header" style="color: #cccccc;">';
    if (count($quiltsPermissionsRows)) {
        echo '<form name="tile_visibility" action="?a=tile_visibility&i=' . $_id . '" method="post" style="margin: 0px; display: inline;">';
        echo csrfField();
        echo '[ <label for="tile_visibility_select">Visibility:</label> <select class="input_select" name="visibility" id="tile_visibility_select" onChange="this.form.submit();">';
        echo '<option value="-1"' . ($tilesRow['visibility'] == -1 ? ' selected' : '') . '>Pending Approval</option>';
        echo '<option value="0"' . ($tilesRow['visibility'] == 0 ? ' selected' : '') . '>Normal Mode</option>';
        echo '<option value="1"' . ($tilesRow['visibility'] == 1 ? ' selected' : '') . '>Always Visible</option>';
        echo '</select> ]';
        echo '</form>' . "\r\n";
    }
    if (!$quiltsRow['finished']) {
        echo '[ ' . makePostLink('?a=tile_delete&i=' . $_id, 'Delete Tile', 'Delete This Tile') . ' ]<br />' . "\r\n";
    }
    echo '</div>';
    echo '<div style="padding: 5px 0px 0px 0px;"></div>' . "\r\n";
}

echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';

echo '<td style="width: 400px; vertical-align: top;">';
echo '<img src="?g=tile&i=' . $_id . '" alt="Tile image preview" style="width: ' . $quiltsRow['tile_width'] . 'px; height: ' . $quiltsRow['tile_height'] . 'px;"><br />';
echo '<div style="padding: 10px 0px 0px 0px;"></div>';
if ($tilesRow['deleted']) {
    echo '<span style="color: red;">This tile has been deleted.</span><br />';
    echo '<div style="padding: 10px 0px 0px 0px;"></div>';
}
if ($GLOBALS['auth']['id'] == $tilesRow['user_id']) {
    echo '<label for="tile_comment">Comment:</label><br />';
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo '<form action="?a=tile_comment&i=' . $_id . '&b=' . encodeUrlPath(server('QUERY_STRING')) . '" method="post" style="margin: 0px; display: inline;">';
    echo csrfField();
    echo '<input type="text" name="comment" id="tile_comment" value="' . safeAttr($tilesRow['comment']) . '" class="input_text" style="width: 200px;"> ';
    echo '<input type="submit" value="Save" class="input_submit">';
    echo '</form>';
    echo '<div style="padding: 10px 0px 0px 0px;"></div>';
} elseif ($tilesRow['comment']) {
    echo 'Comment: ' . safeHtml($tilesRow['comment']) . '<br />';
    echo '<div style="padding: 10px 0px 0px 0px;"></div>';
}
echo 'By: ' . makeLink('?s=profile&u=' . getUsername($tilesRow['user_id']), getUsername($tilesRow['user_id'])) . '<br />';
echo 'Checked Out At: ' . niceDate($tilesRow['started_on']) . '<br />';
echo 'Checked In At: ' . niceDate($tilesRow['posted_on']) . '<br />';
echo '<div style="padding: 10px 0px 0px 0px;"></div>';
echo 'Checkout Tile:<br /><br />';
echo '<img src="?g=tile_sides&i=' . $tilesRow['quilt_id'] . '&x=' . $tilesRow['matrix_x'] . '&y=' . $tilesRow['matrix_y'] . '&borders=' . $tilesRow['borders'] . '" alt="Tile with border preview" style="width: ' . ($quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2) . 'px; height: ' . ($quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2) . 'px;">' . "\r\n";
echo '</td>';

$tilezoom = cookie('tile_zoom', 1);

echo '<td style="padding-left: 10px; text-align: right; vertical-align: top;">';
echo '<table style="border-collapse: collapse; border-spacing: 0;" role="presentation">' . "\r\n";
echo '<tr>' . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] - 1, $tilesRow['matrix_y'] - 1, $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y'] - 1, $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] + 1, $tilesRow['matrix_y'] - 1, $tilezoom);
echo $output . "\r\n";
echo '</tr>' . "\r\n";
echo '<tr>' . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] - 1, $tilesRow['matrix_y'], $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y'], $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] + 1, $tilesRow['matrix_y'], $tilezoom);
echo $output . "\r\n";
echo '</tr>' . "\r\n";
echo '<tr>' . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] - 1, $tilesRow['matrix_y'] + 1, $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y'] + 1, $tilezoom);
echo $output . "\r\n";
list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'] + 1, $tilesRow['matrix_y'] + 1, $tilezoom);
echo $output . "\r\n";
echo '</tr>' . "\r\n";
echo '</table>' . "\r\n";
echo '</td>';

echo '</tr></table>' . "\r\n";

echo '</div>';

comments($_id, 'tiles_comments');

include('../include/parts/footer.php');

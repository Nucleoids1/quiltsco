<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'tiles_completed';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$entries['num'] = (new \Databases\Tiles())->countByUserNotDeleted($GLOBALS['auth']['id']);
$pageCount = ceil($entries['num'] / 25);
$pages = '<span class="pages">Page: ' . pages('?s=tiles_completed', $_page, $pageCount) . '</span><br />';
?>

<h1 class="header">Completed Tiles<?php echo $entries['num'] ? '<br />' . $pages : '' ?></h1>

<div class="content" style="padding: 0px 5px 5px 5px;">
    <?php
    $tilesRows = (new \Databases\Tiles())->selectByUserNotDeletedPage($GLOBALS['auth']['id'], 25, ($_page - 1) * 25);
    if ($tilesRows) {
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td>';
        foreach ($tilesRows as $tilesRow) {
            $quiltsRow = (new \Databases\Quilts())->findById($tilesRow['quilt_id']);
            if ($quiltsRow) {
                $displayAtFullSize = true;
                if ($displayAtFullSize) {
                    $newWidth = $quiltsRow['tile_width'];
                    $newHeight = $quiltsRow['tile_height'];
                } else {
                    if ($quiltsRow['tile_width'] > 100) {
                        $newWidth = 100;
                        $newHeight = $quiltsRow['tile_height'] / ($quiltsRow['tile_width'] / 100);
                        if ($newHeight > 100) {
                            $newWidth = $quiltsRow['tile_width'] / ($quiltsRow['tile_height'] / 100);
                            $newHeight = 100;
                        }
                    } elseif ($quiltsRow['tile_height'] > 100) {
                        $newHeight = 100;
                        $newWidth = $quiltsRow['tile_width'] / ($quiltsRow['tile_height'] / 100);
                        if ($newHeight > 100) {
                            $newHeight = $quiltsRow['tile_height'] / ($quiltsRow['tile_width'] / 100);
                            $newWidth = 100;
                        }
                    } else {
                        $newWidth = $quiltsRow['tile_width'];
                        $newHeight = $quiltsRow['tile_height'];
                    }
                }
                echo '<div style="width: ' . $newWidth . 'px; height: ' . $newHeight . 'px; margin: 4px 4px 0px 0px; padding: 4px; border: solid 2px black; float: left;">';
                echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td>';
                echo '<a href="?s=tile&i=' . $tilesRow['tile_id'] . '"><img src="?g=tile&i=' . $tilesRow['tile_id'] . '" title="' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment']) . '" alt="Tile by ' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment'] ?: 'No comment') . '" style="width: ' . $newWidth . 'px; height: ' . $newHeight . 'px; border: solid 0px black;"></a><br />';
                echo '</td></tr></table>';
                echo '</div>';
            }
        }
        echo '</td></tr></table>';
    } else {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>You do not have any tiles completed.';
    }
    ?>
</div>

<?php
if ($entries['num']) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

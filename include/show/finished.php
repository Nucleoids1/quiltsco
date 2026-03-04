<?php
require_once('../include/functions/pages.php');
require_once('../include/functions/quilt_information.php');
require_once('../include/functions/tile_is_available.php');

$GLOBALS['highlight'] = 'finished';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$quiltsCount = (new \Databases\Quilts())->countFinished();
$pageCount = ceil($quiltsCount / 10);
$pages = '<span class="pages">Page: ' . pages('?s=finished', $_page, $pageCount) . '</span><br />';
?>

<h1 class="header">
    <table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation">
        <tr>
            <td style="vertical-align: top;">
                Quilts<?php echo $quiltsCount ? '<br />' . $pages : '' ?>
                <?php
                if ($GLOBALS['auth']['create_quilt']) {
                    echo '</td><td style="color: #cccccc; font-weight: bold; text-align: right; vertical-align: top;">[ ' . makeLink('?s=quilt_create', 'Create A New Quilt') . ' ]';
                }
                ?>
            </td>
        </tr>
    </table>
</h1>
<?php
$quiltsRows = (new \Databases\Quilts())->selectFinishedPage(10, ($_page - 1) * 10);
if ($quiltsRows) {
    $i = 0;
    echo '<div class="content">' . "\r\n";
    foreach ($quiltsRows as $quiltsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="width: 300px; vertical-align: top; white-space: nowrap;">';
        echo boxImageTop('margin: 0px 5px 0px 0px; width: 300px;');
        echo '<a href="?s=quilt&i=' . $quiltsRow['id'] . '"><img src="?g=quilt_thumb&i=' . $quiltsRow['id'] . '" alt="Finished quilt thumbnail" style="width: 300px; border: 0px;"></a><br />';
        echo boxImageBottom();
        echo '</td><td class="inside" style="padding-left: 10px; font-size: 1.0rem; vertical-align: top;">';
        echo '<a href="?s=quilt&i=' . $quiltsRow['id'] . '" style="font-weight: bold;">' . $quiltsRow['name'] . '</a><br />' . "\r\n";
        quiltInformation($quiltsRow['id']);
        /*
            $i = 0;
            $tilesRows = (new \Databases\Tiles())->selectRandomVisibleByQuilt($quiltsRow['id']); // Raw SQL kept in DB layer for RAND() ordering
            if ($tilesRows)
            {
                foreach ($tilesRows as $tilesRow)
                {
                    if ($i >= 10)
                    {
                        break;
                    }
                    list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y']);
                    if ($display)
                    {
                        if (!$i)
                        {
                            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                        }
                        echo '<a href="?s=tile&i=' . $tilesRow['tile_id'] . '"><img src="?g=tile&i=' . $tilesRow['tile_id'] . '" title="' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment']) . '" alt="Tile by ' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment'] ?: 'No comment') . '" style="width: 50px; height: 50px; border: 0px;"></a>' . "\r\n";
                        $i++;
                    }
                }
            }
            */
        echo '</td></tr></table>' . "\r\n";
    }
    echo '</div>' . "\r\n";
} else {
    echo '<div class="content">Sorry there are no quilts on the site.</div>';
}
if ($quiltsCount) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

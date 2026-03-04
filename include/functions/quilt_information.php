<?php
function quiltInformation($id)
{
    $quiltsRow = (new \Databases\Quilts())->findById($id);
    if ($quiltsRow) {
        $tilesCount = (new \Databases\Tiles())->countVisibleByQuilt($quiltsRow['id']);
        $secondsCount = (new \Databases\Tiles())->sumVisibleSecondsByQuilt($quiltsRow['id']);
        $distinctCount = (new \Databases\Tiles())->countDistinctVisibleUsersByQuilt($quiltsRow['id']);
        echo 'Started ' . niceDate($quiltsRow['posted_on'], 'F j, Y') . ' and last modified on ' .  niceDate($quiltsRow['modified_on'], 'F j, Y') . '<br />' . "\r\n";
        echo 'Dimensions: (' . $quiltsRow['quilt_width'] . ' x ' . $quiltsRow['quilt_height'] . ') tiles @ (' . $quiltsRow['tile_width'] . ' x ' . $quiltsRow['tile_height'] . ') pixels.<br /><br />' . "\r\n";
        echo '<table style="border-collapse: collapse; border-spacing: 0;">';
        echo '<tr>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Quilt Level:</td><td style="width: 180px">';
        echo $quiltsRow['level'] == 0 ? 'Beginner' : '';
        echo $quiltsRow['level'] == 1 ? 'Intermediate' : '';
        echo $quiltsRow['level'] == 2 ? 'Advanced' : '';
        echo '</td>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Tiles Visible:</td><td style="width: 300px">';
        echo $quiltsRow['show_all'] == 0 ? 'When Neighbours Are Finished' : '';
        echo $quiltsRow['show_all'] == 1 ? 'Instantely' : '';
        echo $quiltsRow['show_all'] == 2 ? 'When Whole Quilt Is Complete' : '';
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Side Pixels To Display:</td><td style="width: 180px">' . $quiltsRow['side_pixels'] . '</td>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Can Work On Tiles:</td><td style="width: 220px;">' . ($quiltsRow['work_on_all'] ? 'Even Ones Next To Your Own' : 'Not Next To Your Own') . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Photographs Allowed:</td><td style="width: 180px;">' . ($quiltsRow['photographs_allowed'] ? 'Yes' : 'No') . '</td>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Checkout Tile:</td><td style="width: 220px;">' . ($quiltsRow['start_anywhere'] ? 'Anywhere That\'s Available' : 'Only Next To An Existing Tile') . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Edge Wrap:</td><td style="width: 180px;">';
        echo $quiltsRow['edge_wrap'] == 0 ? 'No' : '';
        echo $quiltsRow['edge_wrap'] == 1 ? 'Horizontal' : '';
        echo $quiltsRow['edge_wrap'] == 2 ? 'Verticle' : '';
        echo $quiltsRow['edge_wrap'] == 3 ? 'All Edges' : '';
        echo '</td>';
        echo '<td style="width: 200px; padding-right: 5px; text-align: right; white-space: nowrap;">Moderated:</td><td style="width: 220px;">' . ($quiltsRow['moderated'] ? 'Yes' : 'No') . '</td>';
        echo '</tr>';
        echo '</table><br />' . "\r\n";
        echo $tilesCount . ' of ' . $quiltsRow['quilt_width'] * $quiltsRow['quilt_height'] . ' (' . round($tilesCount / ($quiltsRow['quilt_width'] * $quiltsRow['quilt_height']) * 100, 2) . '%) tiles completed, representing ' . round($secondsCount / 3600, 2) . ' hours of work by ' . $distinctCount . ' artists.' . "\r\n";
        if ($quiltsRow['finished'] == 0) {
            $tilesPendingCount = (new \Databases\TilesPending())->countByQuilt($quiltsRow['id']);
            echo 'There ' . ($tilesPendingCount != 1 ? 'are' : 'is') . ' currently ' . $tilesPendingCount . ' tile' . ($tilesPendingCount != 1 ? 's' : '') . ' checked out.';
        }
    }
}

<?php
    require_once('../include/functions/pages.php');

    $_id = getInt('i');
    $_page = getInt('p', 1);

    $tilesPending = 0;
    $tilesNone = 0;
    $tilesRows = (new \Databases\Tiles())->selectCachedByUser($_id);
    if (!$tilesCount = count($tilesRows))
    {
        $tilesRows = (new \Databases\Tiles())->selectNonCachedByUser($_id);
        if (count($tilesRows))
        {
            $tilesPending = 1;
        }
        else
        {
            $tilesNone = 1;
        }
    }

    $tilesPageCount = max(1, intval(ceil($tilesCount / 25)));
    echo '<div class="header">Tiles Completed<br />Page: ' . pagesAjax("javascript:tilesCompleted(%%PAGE%%);", $_page, $tilesPageCount) . '</div>';
    echo '<div class="content" style="padding: 0px 5px 5px 5px;">';
    if ($tilesPending == 1)
    {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>This member has completed some tiles but none of them are set to display...';
    }
    elseif ($tilesNone == 1)
    {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>This member has not completed any tiles...';
    }
    else
    {
        echo '<table role="presentation" style="width: 100%; border-spacing: 0;"><tr><td style="padding: 0;">';
        $tilesRows = (new \Databases\Tiles())->selectCachedByUser($_id, 25, ($_page - 1) * 25);
        foreach ($tilesRows as $tilesRow)
        {
            $quiltsRow = (new \Databases\Quilts())->findById($tilesRow['quilt_id']);
            if ($quiltsRow)
            {
                if ($quiltsRow['tile_width'] > 100)
                {
                    $newWidth = 100;
                    $newHeight = $quiltsRow['tile_height'] / ($quiltsRow['tile_width'] / 100);
                    if ($newHeight > 100)
                    {
                        $newWidth = $quiltsRow['tile_width'] / ($quiltsRow['tile_height'] / 100);
                        $newHeight = 100;
                    }
                }
                elseif ($quiltsRow['tile_height'] > 100)
                {
                    $newHeight = 100;
                    $newWidth = $quiltsRow['tile_width'] / ($quiltsRow['tile_height'] / 100);
                    if ($newHeight > 100)
                    {
                        $newHeight = $quiltsRow['tile_height'] / ($quiltsRow['tile_width'] / 100);
                        $newWidth = 100;
                    }
                }
                else
                {
                    $newWidth = $quiltsRow['tile_width'];
                    $newHeight = $quiltsRow['tile_height'];
                }
                echo boxImageTop();
                echo '<table role="presentation" style="width: 100px; height: 100px; border-spacing: 0;"><tr><td style="padding: 0; vertical-align: top;">';
                echo '<a href="?s=tile&i=' . $tilesRow['tile_id'] . '"><img src="?g=tile&i=' . $tilesRow['tile_id'] . '" title="' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment']) . '" alt="Tile by ' . safeAttr(getUsername($tilesRow['user_id'])) . ': ' . safeAttr($tilesRow['comment'] ?: 'No comment') . '" style="width: ' . $newWidth . 'px; height: ' . $newHeight . 'px; border: solid 0px black;"></a><br />';
                echo '</td></tr></table>';
                echo boxImageBottom();
            }
        }
        echo '</td></tr></table>';
    }
    echo '</div>';

<?php
function tileGetSides($id, $x, $y, $center = 0)
{
    $return = '';
    $quiltsRow = (new \Databases\Quilts())->findById($id);
    if ($quiltsRow) {
        //DO THE WRAP AROUND
        $xPlus = $x + 1;
        $xMinus = $x - 1;
        $yPlus = $y + 1;
        $yMinus = $y - 1;
        if ($quiltsRow['edge_wrap'] == 1 || $quiltsRow['edge_wrap'] == 3) {
            if ($xPlus > $quiltsRow['quilt_width']) {
                $xPlus = 1;
            }
            if ($xMinus < 1) {
                $xMinus = $quiltsRow['quilt_width'];
            }
        }
        if ($quiltsRow['edge_wrap'] == 2 || $quiltsRow['edge_wrap'] == 3) {
            if ($yPlus > $quiltsRow['quilt_height']) {
                $yPlus = 1;
            }
            if ($y < 1) {
                $yMinus = $quiltsRow['quilt_height'];
            }
        }
        //TOP LEFT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xMinus, $yMinus);
        if ($tilesRow) {
            $return .= 'TL';
        }
        //TOP CENTER
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $x, $yMinus);
        if ($tilesRow) {
            $return .= 'TC';
        }
        //TOP RIGHT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xPlus, $yMinus);
        if ($tilesRow) {
            $return .= 'TR';
        }
        //MIDDLE LEFT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xMinus, $y);
        if ($tilesRow) {
            $return .= 'ML';
        }
        if ($center) {
            //MIDDLE CENTER
            $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $x, $y);
            if ($tilesRow) {
                $return .= 'MC';
            }
        }
        //MIDDLE RIGHT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xPlus, $y);
        if ($tilesRow) {
            $return .= 'MR';
        }
        //BOTTOM LEFT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xMinus, $yPlus);
        if ($tilesRow) {
            $return .= 'BL';
        }
        //BOTTOM CENTER
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $x, $yPlus);
        if ($tilesRow) {
            $return .= 'BC';
        }
        //BOTTOM RIGHT
        $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixVisible($id, $xPlus, $yPlus);
        if ($tilesRow) {
            $return .= 'BR';
        }
    }
    return $return;
}

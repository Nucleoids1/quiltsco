<?php
require_once('../include/functions/tile_is_available.php');

function createQuiltImageCache($id)
{
    $quiltsRow = (new \Databases\Quilts())->findById($id);
    if ($quiltsRow) {
        $imDest = imagecreatetruecolor($quiltsRow['quilt_width'] * $quiltsRow['tile_width'], $quiltsRow['quilt_height'] * $quiltsRow['tile_height']);
        $transColor = imagecolorallocate($imDest, 0, 0, 1);
        imagefill($imDest, 0, 0, $transColor);
        for ($y = 1; $y <= $quiltsRow['quilt_height']; $y++) {
            for ($x = 1; $x <= $quiltsRow['quilt_width']; $x++) {
                $tilesRow = (new \Databases\Tiles())->findByQuiltAndMatrixNotDeleted($id, $x, $y);
                if ($tilesRow) {
                    list($available, $display, $output) = tileIsAvailable($tilesRow['quilt_id'], $tilesRow['matrix_x'], $tilesRow['matrix_y'], 1, 1);
                    if ($display) {
                        (new \Databases\Tiles())->setCacheDisplay($tilesRow['tile_id'], 1);
                        $imSrc = imagecreatefromstring($tilesRow['data_tile']);
                        imagecopy($imDest, $imSrc, $x * $quiltsRow['tile_width'] - $quiltsRow['tile_width'], $y * $quiltsRow['tile_height'] - $quiltsRow['tile_height'], 0, 0, $quiltsRow['tile_width'], $quiltsRow['tile_height']);
                    } else {
                        (new \Databases\Tiles())->setCacheDisplay($tilesRow['tile_id'], 0);
                    }
                }
            }
        }
        $temp = makeCacheCode();
        imagejpeg($imDest, '../temp/' . $temp, 90);
        $imgData = file_get_contents('../temp/' . $temp);
        unlink('../temp/' . $temp);
        (new \Databases\Quilts())->saveFullJpg($id, $imgData);
        $temp = makeCacheCode();
        imagepng($imDest, '../temp/' . $temp);
        $imgData = file_get_contents('../temp/' . $temp);
        unlink('../temp/' . $temp);
        (new \Databases\Quilts())->saveFullPng($id, $imgData);
        $widthOrig = $quiltsRow['quilt_width'] * $quiltsRow['tile_width'];
        $heightOrig = $quiltsRow['quilt_height'] * $quiltsRow['tile_height'];
        $width = 300;
        $height = 300;
        if ($width > $quiltsRow['quilt_width'] * $quiltsRow['tile_width']) {
            $width = $quiltsRow['quilt_width'] * $quiltsRow['tile_width'];
        }
        if ($height > $quiltsRow['quilt_height'] * $quiltsRow['tile_height']) {
            $height = $quiltsRow['quilt_height'] * $quiltsRow['tile_height'];
        }
        if ($width && ($widthOrig < $heightOrig)) {
            $width = ($height / $heightOrig) * $widthOrig;
        } else {
            $height = ($width / $widthOrig) * $heightOrig;
        }
        $width = max(1, (int)round($width));
        $height = max(1, (int)round($height));
        $imageP = imagecreatetruecolor($width, $height);
        imagecopyresampled($imageP, $imDest, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
        imagecolortransparent($imageP, $transColor);
        $temp = makeCacheCode();
        imagepng($imageP, '../temp/' . $temp);
        $imgData = file_get_contents('../temp/' . $temp);
        unlink('../temp/' . $temp);
        (new \Databases\Quilts())->saveThumbnail($id, $imgData);
    }
}

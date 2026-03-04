<?php
require_once('../include/functions/filename_extension.php');
require_once('../include/functions/image_directory.php');

set_time_limit(0);
ignore_user_abort(1);

// PLACE 1 : 0 = NOT NEW | # = NEW FILE
// PLACE 2 : 0 = NOT DUPE | # = DUPE FILE NUMBER
// PLACE 3 : NOTICE TO DISPLAY

function safeZipImageName($name)
{
    $name = str_replace('\\', '/', $name);
    $name = ltrim($name, '/');
    if ($name === '' || strpos($name, '../') !== false || strpos($name, '..\\') !== false) {
        return '';
    }
    $basename = basename($name);
    if ($basename === '' || $basename === '.' || $basename === '..') {
        return '';
    }
    if (!preg_match('/\.(jpe?g|png|gif)$/i', $basename)) {
        return '';
    }
    return $basename;
}

function processUploads()
{
    $_picture = files('picture');
    if (!$_picture || !isset($_picture['name']) || !isset($_picture['tmp_name'])) {
        return array(0, 0, 'No File');
    }
    $extension = filenameExtension($_picture['name']);
    $allowMimes = array('image/jpeg', 'image/gif', 'image/png');
    if ($getimagesize = @getimagesize($_picture['tmp_name'])) {
        if (in_array($getimagesize['mime'], $allowMimes)) {
            $hash = sha1_file($_picture['tmp_name']);
            $size = filesize($_picture['tmp_name']);
            $dupe = (new \Databases\ImagesHashes())->findByFullHashAndSize($hash, $size);
            if ($dupe) {
                return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . $_picture['name']);
            }
            $dupe = (new \Databases\ImagesHashes())->findByThumbHashAndSize($hash, $size);
            if ($dupe) {
                return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . $_picture['name']);
            }
            $dupe = (new \Databases\ImagesHashes())->findByOriginalHashAndSize($hash, $size);
            if ($dupe) {
                return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . $_picture['name']);
            }
            if ($getimagesize['mime'] == 'image/jpeg') {
                $handle = @imagecreatefromjpeg($_picture['tmp_name']);
            } elseif ($getimagesize['mime'] == 'image/png') {
                $handle = @imagecreatefrompng($_picture['tmp_name']);
            } elseif ($getimagesize['mime'] == 'image/gif') {
                $handle = @imagecreatefromgif($_picture['tmp_name']);
            }
            if (isset($handle) && $handle) {
                if ($getimagesize[0] >= THUMB_WIDTH && $getimagesize[1] >= THUMB_HEIGHT) {
                    $pictureId = (new \Databases\Images())->createForUser($GLOBALS['auth']['id'], date('Y-m-d H:i:s', server('REQUEST_TIME')));
                    (new \Databases\ImagesBinaries(binariesPath($pictureId)))->createTable();
                    (new \Databases\ImagesBinaries(binariesPath($pictureId)))->createRecord((int) $pictureId);
                    (new \Databases\ImagesHashes())->createRecord($pictureId, $hash, $size, $hash, $size);
                    $shrinkSize = shrinkImage($handle, $_picture['tmp_name'], $getimagesize['mime'], IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT, $pictureId);
                    $thumbSize = thumbImage($handle, $getimagesize['mime'], THUMB_WIDTH, THUMB_HEIGHT, $pictureId);
                    list($shrinkWidth, $shrinkHeight) = $shrinkSize;
                    list($thumbWidth, $thumbHeight) = $thumbSize;
                    if ($shrinkWidth && $shrinkHeight && $thumbWidth && $thumbHeight) {
                        (new \Databases\Images())->updateImageDimensions($pictureId, $shrinkWidth, $shrinkHeight, $getimagesize['mime']);
                        return array($pictureId, 0, 'Image Accepted - Filename: ' . $_picture['name']);
                    }
                    (new \Databases\Images())->deleteById($pictureId);
                    (new \Databases\ImagesBinaries(binariesPath($pictureId)))->deleteImageData($pictureId);
                    (new \Databases\ImagesHashes())->deleteImageHash($pictureId);
                    (new \Databases\Images())->resetLegacyAutoIncrement();
                    return array(0, 0, 'Corrupt Image - Filename: ' . $_picture['name']);
                }
                return array(0, 0, 'Image Too Small - Filename: ' . $_picture['name']);
            }
            return array(0, 0, 'Can Not Create Handle - Filename: ' . $_picture['name']);
        }
        return array(0, 0, 'Invalid Mime Type: ' . $getimagesize['mime'] . ' - Filename: ' . $_picture['name']);
    } elseif ($extension == 'zip') {
        if (function_exists('process_image')) {
            $za = new ZipArchive();
            $res = $za->open($_picture['tmp_name']);
            if ($res === TRUE) {
                $fullNotice = '';
                for ($i = 0; $i < $za->numFiles; $i++) {
                    $info = $za->statIndex($i);
                    if ($info['size'] < 7500000) {
                        $entryName = $za->getNameIndex($i);
                        $safeEntryName = safeZipImageName($entryName);
                        if (!$safeEntryName) {
                            $fullNotice .= 'Invalid Zip Entry - Filename: ' . $entryName . '<br />';
                            continue;
                        }

                        $tempName = makeCacheCode() . '_' . $safeEntryName;
                        $tempPath = '../temp/' . $tempName;
                        $imString = $za->getFromIndex($i);
                        file_put_contents($tempPath, $imString);
                        if ($getimagesize = @getimagesize($tempPath)) {
                            if (in_array($getimagesize['mime'], $allowMimes)) {
                                list($new, $dupe, $notice) = processFile($tempPath);
                                process_image($new, $dupe);
                                $fullNotice .= $notice . '<br />';
                            } else {
                                $fullNotice .= 'Invalid File Type - Filename: ' . $entryName . ' Mime: ' . $getimagesize['mime'] . '<br />';
                            }
                        } else {
                            $fullNotice .= 'Corrupt Image - Filename: ' . $entryName . '<br />';
                        }
                        @unlink($tempPath);
                    } else {
                        $fullNotice .= 'File Too Large - Filename: ' . $za->getNameIndex($i) . '<br />';
                    }
                }
                $za->close();
                return array(0, 0, str_replace('../temp/', '', $fullNotice));
            }
            return array(0, 0, 'Corrupt Zip Archive - Filename: ' . $_picture['name']);
        }
        return array(0, 0, 'You\'re not allowed to upload zip files to this section.');
    }
    return array(0, 0, 'Corrupt Image - Filename: ' . $_picture['name']);
}

function processFile($file)
{
    if ($getimagesize = @getimagesize($file)) {
        if ($getimagesize['mime'] != 'image/jpeg' && $getimagesize['mime'] != 'image/png' && $getimagesize['mime'] != 'image/gif') {
            return array(0, 0, 'Invalid File Type - Mime: ' . $getimagesize['mime'] . ')');
        }
        $hash = sha1_file($file);
        $size = filesize($file);
        $dupe = (new \Databases\ImagesHashes())->findByFullHashAndSize($hash, $size);
        if ($dupe) {
            return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . basename($file));
        }
        $dupe = (new \Databases\ImagesHashes())->findByThumbHashAndSize($hash, $size);
        if ($dupe) {
            return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . basename($file));
        }
        $dupe = (new \Databases\ImagesHashes())->findByOriginalHashAndSize($hash, $size);
        if ($dupe) {
            return array(0, $dupe['image_id'], 'Duplicate Image - Filename: ' . basename($file));
        }
        if ($getimagesize['mime'] == 'image/jpeg') {
            $handle = @imagecreatefromjpeg($file);
        } elseif ($getimagesize['mime'] == 'image/png') {
            $handle = @imagecreatefrompng($file);
        } elseif ($getimagesize['mime'] == 'image/gif') {
            $handle = @imagecreatefromgif($file);
        }
        if (isset($handle) && $handle) {
            if ($getimagesize[0] >= THUMB_WIDTH && $getimagesize[1] >= THUMB_HEIGHT) {
                $pictureId = (new \Databases\Images())->createForUser($GLOBALS['auth']['id'], date('Y-m-d H:i:s'));
                (new \Databases\ImagesBinaries(binariesPath($pictureId)))->createTable();
                (new \Databases\ImagesBinaries(binariesPath($pictureId)))->createRecord((int) $pictureId);
                (new \Databases\ImagesHashes())->createRecord($pictureId, $hash, $size, $hash, $size);
                $shrinkSize = shrinkImage($handle, $file, $getimagesize['mime'], IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT, $pictureId);
                $thumbSize = thumbImage($handle, $getimagesize['mime'], THUMB_WIDTH, THUMB_HEIGHT, $pictureId);
                list($shrinkWidth, $shrinkHeight) = $shrinkSize;
                list($thumbWidth, $thumbHeight) = $thumbSize;
                if ($shrinkWidth && $shrinkHeight && $thumbWidth && $thumbHeight) {
                    (new \Databases\Images())->updateImageDimensions($pictureId, $shrinkWidth, $shrinkHeight, $getimagesize['mime']);
                    return array($pictureId, 0, 'Image Accepted (' . basename($file) . ')');
                }
                (new \Databases\Images())->deleteById($pictureId);
                (new \Databases\ImagesBinaries(binariesPath($pictureId)))->deleteImageData($pictureId);
                (new \Databases\ImagesHashes())->deleteImageHash($pictureId);
                (new \Databases\Images())->resetLegacyAutoIncrement();
                return array(0, 0, 'Corrupt Image - Filename: ' . basename($file));
            }
            return array(0, 0, 'Image Too Small - Filename: ' . basename($file));
        }
        return array(0, 0, 'Corrupt Image - Filename: ' . basename($file));
    }
    return array(0, 0, 'Invalid File: ' . basename($file));
}

function shrinkImage($handle, $srcFile, $dstType, $dstWidth, $dstHeight, $pictureId, $force = 0)
{
    $srcWidth = imagesx($handle);
    $srcHeight = imagesy($handle);
    if ($srcWidth > $dstWidth || $srcHeight > $dstHeight || $force) {
        if ($srcWidth > $dstWidth) {
            $dstHeight = $srcHeight / ($srcWidth / $dstWidth);
        } elseif ($srcWidth > $dstHeight) {
            $dstWidth = $srcWidth  / ($srcHeight / $dstHeight);
        } else {
            $dstHeight = $srcHeight;
            $dstWidth = $srcWidth;
        }
        $newHandle = imagecreatetruecolor($dstWidth, $dstHeight);
        if (!$newHandle) {
            return array(0, 0);
        }
        if (!imagecopyresampled($newHandle, $handle, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight)) {
            return array(0, 0);
        }
        $temp = makeCacheCode();
        if ($dstType == 'image/jpeg') {
            imagejpeg($newHandle, '../temp/' . $temp, 90);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } elseif ($dstType == 'image/png') {
            imagepng($newHandle, '../temp/' . $temp);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } elseif ($dstType == 'image/gif') {
            imagegif($newHandle, '../temp/' . $temp);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } else {
            return array(0, 0);
        }
        (new \Databases\ImagesBinaries(binariesPath($pictureId)))->updateFullImageData($pictureId, $imgData);
        (new \Databases\ImagesHashes())->updateFullHash($pictureId, sha1($imgData), strlen($imgData));
        return array($dstWidth, $dstHeight);
    }
    $imgData = file_get_contents($srcFile);
    (new \Databases\ImagesBinaries(binariesPath($pictureId)))->updateFullImageData($pictureId, $imgData);
    return array($srcWidth, $srcHeight);
}

function thumbImage($handle, $dstType, $dstWidth, $dstHeight, $pictureId)
{
    $srcWidth  = imagesx($handle);
    $srcHeight = imagesy($handle);
    if ($srcWidth >= $dstWidth && $srcHeight >= $dstHeight) {
        $newHandle = imagecreatetruecolor($dstWidth, $dstHeight);
        if (!$newHandle) {
            return array(0, 0);
        }
        if ($srcHeight < $srcWidth) {
            $cpyWidth = round($dstWidth * $srcHeight / $dstHeight);
            if ($cpyWidth > $srcWidth) {
                $cpyWidth = $srcWidth;
                $cpyHeight = round($dstHeight * $srcWidth / $dstWidth);
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            } else {
                $cpyHeight = $srcHeight;
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            }
        } else {
            $cpyHeight = round($dstHeight * $srcWidth / $dstWidth);
            if ($cpyHeight > $srcHeight) {
                $cpyHeight = $srcHeight;
                $cpyWidth = round($dstWidth * $srcHeight / $dstHeight);
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            } else {
                $cpyWidth = $srcWidth;
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            }
        }
        if (!imagecopyresampled($newHandle, $handle, 0, 0, $xOffset, $yOffset, $dstWidth, $dstHeight, $cpyWidth, $cpyHeight)) {
            return array(0, 0);
        }
        $temp = makeCacheCode();
        if ($dstType == 'image/jpeg') {
            imagejpeg($newHandle, '../temp/' . $temp, 90);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } elseif ($dstType == 'image/png') {
            imagepng($newHandle, '../temp/' . $temp);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } elseif ($dstType == 'image/gif') {
            imagegif($newHandle, '../temp/' . $temp);
            $imgData = file_get_contents('../temp/' . $temp);
            unlink('../temp/' . $temp);
        } else {
            return array(0, 0);
        }
        (new \Databases\ImagesBinaries(binariesPath($pictureId)))->updateThumbImageData($pictureId, $imgData);
        (new \Databases\ImagesHashes())->updateThumbHash($pictureId, sha1($imgData), strlen($imgData));
        return array($dstWidth, $dstHeight);
    } else {
        return array(0, 0);
    }
}

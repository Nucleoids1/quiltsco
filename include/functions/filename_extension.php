<?php
function filenameExtension($filename)
{
    $pos = strrpos($filename, '.');
    if ($pos === false) {
        return false;
    } else {
        return strtolower(substr($filename, $pos + 1));
    }
}

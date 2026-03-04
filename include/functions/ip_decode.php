<?php
function decodeIp($int)
{
    if ($int == "") {
        return 0;
    } else {
        $w = (int)(($int / 16777216) - 256 * floor(($int / 16777216) / 256));
        $x = (int)(($int / 65536) - 256 * floor(($int / 65536) / 256));
        $y = (int)(($int / 256) - 256 * floor(($int / 256) / 256));
        $z = (int)(($int) - 256 * floor(($int) / 256));
        return ($w . '.' . $x . '.' . $y . '.' . $z);
    }
}

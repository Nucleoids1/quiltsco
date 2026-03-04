<?php
function encodeIp($ip)
{
    if ($ip == '') {
        return 0;
    } else {
        $ips = explode(".", $ip);
        return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
    }
}

function ipEncode($ip)
{
    if ($ip == '') {
        return 0;
    } else {
        $ips = explode(".", $ip);
        return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
    }
}

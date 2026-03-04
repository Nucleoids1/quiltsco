<?php
function closestWord($sentince, $chars = 250)
{
    if (strlen($sentince) > $chars) {
        $temp = substr($sentince, 0, $chars + 1);
        for ($i = $chars; $i > 1; $i--) {
            if (substr($temp, $i, 1) == ' ') {
                $temp = substr($temp, 0, $i);
                for ($j = $i - 1; $j > 1; $j--) {
                    if ((ord(substr($temp, $j, 1)) >= 48 && ord(substr($temp, $j, 1)) <= 57) || (ord(substr($temp, $j, 1)) >= 65 && ord(substr($temp, $j, 1)) <= 122)) {
                        $sentince = substr($temp, 0, $j + 1) . '...';
                        break;
                    }
                }
                break;
            }
        }
    }
    return $sentince;
}

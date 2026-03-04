<?php
function imageDirectory($id, $numb = 5000)
{
    $num = ceil($id / intval($numb));
    $high = $num * intval($numb);
    $low = $high - intval($numb - 1);
    return substr('00000000' . $low, -8) . '/';
}

function binariesPath($id, $numb = 5000)
{
    $num = ceil($id / intval($numb));
    $high = $num * intval($numb);
    $low = $high - intval($numb - 1);
    return substr('00000000' . $low, -8);
}

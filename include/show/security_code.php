<?php
$imgWidth = 175;
$imgHeight = 50;

$handle = imagecreatetruecolor($imgWidth, $imgHeight);

$colA = array(mt_rand(175, 255), mt_rand(175, 255), mt_rand(175, 255));
$colB = array(mt_rand(175, 255), mt_rand(175, 255), mt_rand(175, 255));
imagecolorgradient($handle, 0, 0, $imgWidth, $imgHeight, $colA, $colB);

//random lines
$num = mt_rand(50, 80);
for ($i = 0; $i < $num; $i++) {
    randomLine($handle);
}

$currentX = mt_rand(10, 25);

$length = mt_rand(5, 6);
$charArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');

$code = '';

for ($i = 0; $i < $length; $i++) {
    $char = $charArr[mt_rand(0, count($charArr) - 1)];
    apendLetter($handle, $char);
    $code .= $char;
}

header('Content-type: image/png');
imagepng($handle);

if ($GLOBALS['auth']['id']) {
    (new \Databases\SecurityCodeLast())->replaceForUser($GLOBALS['auth']['id'], $code);
} elseif (cookie('cache')) {
    (new \Databases\SecurityCodeCache())->replaceForCache(cookie('cache'), $code);
}

function apendLetter($handle, $letter)
{
    global $currentX;
    $gitter = 40 + (mt_rand(-3, 3));
    $font = '../include/fonts/dirtyheadline.ttf';
    $color = imagecolorallocate($handle, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
    imagettftext($handle, 32, mt_rand(-10, 10), $currentX, $gitter, $color, $font, $letter);
    $currentX += 20 + mt_rand(-2, 6);
}

function imagecolorgradient($img, $x1, $y1, $width, $height, $colA, $colB)
{
    $varC1 = ($colA[0] - $colB[0]) / $height;
    $varC2 = ($colA[1] - $colB[1]) / $height;
    $varC3 = ($colA[2] - $colB[2]) / $height;
    for ($i = 0; $i <= $height; $i++) {
        $col = imagecolorallocate($img, $colA[0] - floor($i * $varC1), $colA[1] - floor($i * $varC2),	$colA[2] - floor($i * $varC3));
        imageline($img, $x1, $y1 + $i, $x1 + $width, $y1 + $i, $col);
    }
}

function randomLine($handle)
{
    global $imgWidth, $imgHeight;
    $col = imagecolorallocate($handle, mt_rand(180, 255), mt_rand(180, 255), mt_rand(180, 255));
    imageline($handle, mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), mt_rand(0, $imgWidth), mt_rand(0, $imgHeight), $col);
}

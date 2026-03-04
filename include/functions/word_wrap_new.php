<?php
function wordWrapNew($sentince, $nl2br = 0, $chars = 50)
{
    $return = '';
    while (strstr($sentince, '  ')) {
        $sentince = str_replace('  ', ' ', $sentince);
    }
    if ($nl2br) {
        $sentince = str_replace("\r", '', str_replace("\n", ' [CRLF] ', ' ' . $sentince . ' '));
    } else {
        $sentince = str_replace("\r", '', str_replace("\n", ' ', ' ' . $sentince . ' '));
    }
    $words = explode(' ', $sentince);
    foreach ($words as $word) {
        if ($word) {
            if (strstr(strtolower($word), 'http://') !== false || strstr(strtolower($word), 'https://') !== false || strstr(strtolower($word), '.com') !== false || strstr(strtolower($word), '.org') !== false || strstr(strtolower($word), '.net') !== false) {
                if (strlen($word) <= 40) {
                    if (strstr(strtolower($word), '@')) {
                        $return .= ($return ? ' ' : '') . '<a href="mailto:">' . $word . '</a>';
                    } else {
                        $return .= ($return ? ' ' : '') . '<a href="' . (substr($word, 0, 7) == 'http://' ? '' : (substr($word, 0, 7) == 'https://' ? '' : 'https://')) . $word . '">' . (substr($word, 0, 7) == 'http://' ? '' : (substr($word, 0, 7) == 'https://' ? '' : 'https://')) . $word . '</a>';
                    }
                } else {
                    if (strstr(strtolower($word), '@')) {
                        $return .= ($return ? ' ' : '') . '[<a href="mailto:">email</a>]';
                    } else {
                        $return .= ($return ? ' ' : '') . '[<a href="' . (substr($word, 0, 7) == 'http://' ? '' : (substr($word, 0, 7) == 'https://' ? '' : 'https://')) . $word . '">link</a>]';
                    }
                }
            } else {
                $return .= ($return ? ' ' : '') . wordwrap($word, $chars, ' ', 1);
            }
        }
    }
    if ($nl2br) {
        $return = str_replace('[CRLF]', "\r\n", $return);
    }
    return trim($return);
}

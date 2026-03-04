<?php
function quotes($start, $finish, $string)
{
    if ($string && $start && $finish) {
        while (stristr($string, $start) && stristr($string, $finish)) {
            $posFinish = stripos($string, $finish);
            $posStart = strripos($string, substr($start, 0, $posFinish));
            if ($posFinish !== false && $posStart !== false) {
                $string = substr($string, 0, $posFinish) . '</td></tr></table>' . substr($string, $posFinish + strlen($finish));
                $posStart = strripos($string, substr($start, 0, $posFinish));
                $string = substr($string, 0, $posStart) . '<table style="width: 100%; border-collapse: collapse; background: #;"><tr><td style="border: 1px solid #; padding: 4px;">' . substr($string, $posStart + strlen($start));
            } else {
                break;
            }
        }
    }
    $string = str_ireplace($start, '', $string);
    $string = str_ireplace($finish, '', $string);
    return $string;
}

function replaceEmbededTags($str, $startTag = '[quote]', $endTag = '[/quote]', $startReplacement = '</div>', $endReplacement = '<div style="border: 1px solid; padding: 1em; border-radius: 7px;">')
{
    $startTagLength = strlen($startTag);
    $endTagLength = strlen($endTag);

    $offset = 0;
    $continue = true;

    while (true) {
        $startTagLoc     = stripos($str, $startTag, $offset);
        $endTagLoc     = stripos($str, $endTag, $startTagLoc);

        if ($startTagLoc === false || $endTagLoc === false)
            break;

        //check if there's an open tag between this open and the first found close
        $secondStartTagLoc = stripos($str, $startTag, $startTagLoc + 1);
        if ($secondStartTagLoc !== false && $secondStartTagLoc < $endTagLoc) {
            $offset = $secondStartTagLoc;
            continue;
        }

        $str = substr($str, 0, $endTagLoc) . $startReplacement . substr($str, $endTagLoc + $endTagLength);
        $str = substr($str, 0, $startTagLoc) . $endReplacement . substr($str, $startTagLoc + $startTagLength);

        $offset = 0;
    }

    $str = str_ireplace($startTag, '', $str);
    $str = str_ireplace($endTag, '', $str);

    return $str;
}

function smileys($str)
{
    $communitySmileysRows = (new \Databases\CommunitySmileys())->selectAllByName();
    foreach ($communitySmileysRows as $communitySmileysRow) {
        $str = str_ireplace($communitySmileysRow['name'], '<img src="images/smileys/' . $communitySmileysRow['filename'] . '" style="vertical-align: middle;" alt="" />', $str);
    }
    return $str;
}

function imagesCode($b)
{
    $i = 0;
    $b = ' ' . $b;
    while ($start = strpos($b, '[', $i)) {
        if ($end = strpos($b, ']', $start)) {
            $id = substr($b, $start + 1, $end - $start - 1);
            if (is_numeric($id)) {
                if ($id > 25) {
                    $imagesRow = (new \Databases\Images())->findById($id);
                    if ($imagesRow) {
                        $b = str_replace('[' . $id . ']', '<a href="?s=gallery_image&i=' . $id . '"><img src="?g=full&i=' . $id . '" alt="Image #' . $id . '" style="width: ' . $imagesRow['width'] . 'px; height: ' . $imagesRow['height'] . 'px; border: 1px solid;" /></a>', $b);
                    }
                }
            }
        }
        $i = $start + 1;
    }
    return $b;
}

function bbcodes($body)
{
    $body = str_ireplace("style=", "", $body);
    $body = str_ireplace("[b]", "<b>", $body);
    $body = str_ireplace("[/b]", "</b>", $body);
    $body = str_ireplace("[i]", "<i>", $body);
    $body = str_ireplace("[/i]", "</i>", $body);
    $body = str_ireplace("[u]", "<span style=\"text-decoration: underline;\">", $body);
    $body = str_ireplace("[/u]", "</span>", $body);
    $body = str_ireplace("[img]http://", "[img]", $body);
    $body = str_ireplace("[video]http://", "[video]", $body);
    $body = str_ireplace("[video]www.youtube.com/watch?v=", "[video]www.youtube.com/v/", $body);
    $body = str_ireplace('&amp;autoplay=1', '', $body);
    $body = str_ireplace("[url=http://", "[url=", $body);
    $body = str_ireplace("[link=http://", "[link=", $body);
    $body = str_ireplace("[color=#", "[color=", $body);
    $body = preg_replace("/\[link\](.*?)\[\/link\]/si", "\\1", $body);
    $body = preg_replace("/\[LINK\](.*?)\[\/LINK\]/si", "\\1", $body);
    $body = addlinks($body);
    //$body = quotes('[QUOTE]', '[/QUOTE]', $body);
    $body = replaceEmbededTags($body);
    $body = preg_replace("/\[code\](.*?)\[\/code\]/si", "<pre>\\1</pre>", $body);
    $body = preg_replace("/\[CODE\](.*?)\[\/CODE\]/si", "<pre>\\1</pre>", $body);
    $body = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"http://\\1\" style=\"border: 0;\" alt=\"User uploaded image\">", $body);
    $body = preg_replace("/\[IMG\](.*?)\[\/IMG\]/si", "<img src=\"http://\\1\" style=\"border: 0;\" alt=\"User uploaded image\">", $body);
    $body = preg_replace("/\[VIDEO\](.*?)\[\/VIDEO\]/si", "<object style=\"width: 425px; height: 350px;\"><param name=\"movie\" value=\"http://\\1\"></param><embed src=\"http://\\1\" type=\"application/x-shockwave-flash\" style=\"width: 425px; height: 350px;\"></embed></object>", $body);
    $body = preg_replace("/\[video\](.*?)\[\/video\]/si", "<object style=\"width: 425px; height: 350px;\"><param name=\"movie\" value=\"http://\\1\"></param><embed src=\"http://\\1\" type=\"application/x-shockwave-flash\" style=\"width: 425px; height: 350px;\"></embed></object>", $body);
    $body = preg_replace("/\[url=(.*)](.*)\[\/url\]/si", "<a href=\"http://\\1\">\\2</a>", $body);
    $body = preg_replace("/\[link=(.*)](.*)\[\/link\]/si", "<a href=\"http://\\1\">\\2</a>", $body);
    $body = preg_replace("/\[color=(.*)](.*)\[\/color\]/si", "", $body);
    $body = preg_replace("/\[size=(.*)](.*)\[\/size\]/si", "", $body);
    $body = preg_replace("/\[font=(.*)](.*)\[\/font\]/si", "", $body);
    $body = str_ireplace('[url]', '', $body);
    $body = str_ireplace('[/url]', '', $body);
    $body = str_ireplace('[link]', '', $body);
    $body = str_ireplace('[/link]', '', $body);
    $body = str_ireplace('[email]', '', $body);
    $body = str_ireplace('[/email]', '', $body);
    return trim($body);
}

function addlinks($string)
{
    //$string = eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]@/]", "<a href=\"\\0\">\\0</a>", $string);
    //$string = eregi_replace ('[_a-zA-z0-9\-]+(\.[_a-zA-z0-9\-]+)*\@' . '[_a-zA-z0-9\-]+(\.[a-zA-z]{1,4})+', '<a href="mailto:\\0">\\0</a>', $string);
    $string = makeClickableRep($string);
    return $string;
}

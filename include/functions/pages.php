<?php
function pages($url, $currentPage, $pageCount, $next = false)
{
    $currentPage = $currentPage ? intval($currentPage) : 1;
    $pageCount = $pageCount ? intval($pageCount) : 1;
    $p = $aft = '';
    if ($pageCount > 10) {
        $p .= (1 == $currentPage) ? ' <b>1</b> ' : ' <a href="' . $url . '&p=1">1</a> ';
        $max = 10;
        if (($currentPage - 4) > 2) {
            $p .= ' .. ';
            $second = $currentPage - 4;
            if (($currentPage + 4) < $pageCount) {
                $aft = ' .. ';
                $max = $currentPage + 4;
            } else {
                $max = $pageCount;
                $second = $pageCount - 8;
            }
        } else {
            $second = 2;
            $aft = ' .. ';
        }
        for ($i = $second; $i < $max; $i++) {
            if ($i == $currentPage) {
                $p .= ' <b>' . $i . '</b> ';
            } else {
                $p .= ' <a href="' . $url . '&p=' . $i . '">' . $i . '</a> ';
            }
        }
        $p .= $aft;
        $p .= ($pageCount == $currentPage) ? ' <b>' . $pageCount . ' </b>' : ' <a href="' . $url . '&p=' . $pageCount . '">' . $pageCount . '</a> ';
    } else {
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i == $currentPage) {
                $p .= ' <b>' . $i . '</b> ';
            } else {
                $p .= ' <a href="' . $url . '&p=' . $i . '">' . $i . '</a> ';
            }
        }
    }
    if ($next && $currentPage != $pageCount) {
        $p .= ' <a href="' . $url . '&p=' . ($currentPage + 1) . '">Next &#187;&#187;</a>';
    }
    return $p;
}

function pagesAjax($url, $currentPage, $pageCount, $next = false)
{
    $currentPage = $currentPage ? intval($currentPage) : 1;
    $pageCount = $pageCount ? intval($pageCount) : 1;
    $p = $aft = '';
    if ($pageCount > 10) {
        $p .= (1 == $currentPage) ? ' <b>1</b> ' : ' <a href="' . str_replace('%%PAGE%%', 1, $url) . '">1</a> ';
        $max = 10;
        if (($currentPage - 4) > 2) {
            $p .= ' .. ';
            $second = $currentPage - 4;
            if (($currentPage + 4) < $pageCount) {
                $aft = ' .. ';
                $max = $currentPage + 4;
            } else {
                $max = $pageCount;
                $second = $pageCount - 8;
            }
        } else {
            $second = 2;
            $aft = ' .. ';
        }
        for ($i = $second; $i < $max; $i++) {
            if ($i == $currentPage) {
                $p .= ' <b>' . $i . '</b> ';
            } else {
                $p .= ' <a href="' . str_replace('%%PAGE%%', $i, $url) . '">' . $i . '</a> ';
            }
        }
        $p .= $aft;
        $p .= ($pageCount == $currentPage) ? ' <b>' . $pageCount . ' </b>' : ' <a href="' . str_replace('%%PAGE%%', $pageCount, $url) . '">' . $pageCount . '</a> ';
    } else {
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i == $currentPage) {
                $p .= ' <b>' . $i . '</b> ';
            } else {
                $p .= ' <a href="' . str_replace('%%PAGE%%', $i, $url) . '">' . $i . '</a> ';
            }
        }
    }
    if ($next && $currentPage != $pageCount) {
        $p .= ' <a href="' . str_replace('%%PAGE%%', $currentPage + 1, $url) . '">Next &#187;&#187;</a>';
    }
    return $p;
}

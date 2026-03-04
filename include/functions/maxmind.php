<?php
function ip2country($ip = '')
{
    if (!$ip) {
        $ip = server('HTTP_X_FORWARDED_FOR') ? server('HTTP_X_FORWARDED_FOR') : (server('REMOTE_ADDR') ? server('REMOTE_ADDR') : '127.0.0.1');
    }
    if (!is_numeric($ip)) {
        $ip = ipEncode($ip);
    }
    $maxmindCountriesRow = (new \DatabasesLocation\Ip2location())->findCountryByIpTo($ip);
    if ($maxmindCountriesRow) {
        return $maxmindCountriesRow['country'];
    }
    return '';
}

function ip2flag($ip = '')
{
    if (!$ip) {
        $ip = server('HTTP_X_FORWARDED_FOR') ? server('HTTP_X_FORWARDED_FOR') : (server('REMOTE_ADDR') ? server('REMOTE_ADDR') : '127.0.0.1');
    }
    if (!is_numeric($ip)) {
        $ip = ipEncode($ip);
    }
    $maxmindCountriesRow = (new \DatabasesLocation\Ip2location())->findCountryInfoByIpTo($ip);
    if ($maxmindCountriesRow) {
        if (is_file('images/flags/' . $maxmindCountriesRow['country'] . '.png')) {
            return '<img src="images/flags/' . $maxmindCountriesRow['country'] . '.png" alt="' . safeAttr($maxmindCountriesRow['country_name']) . ' flag" title="' . safeAttr($maxmindCountriesRow['country_name']) . '" />';
        }
    }
    return '<img src="images/flags/unknown.png" alt="Unknown country flag" title="Unknown" />';
}

function ip2locationDownload()
{
    $contents = file_get_contents('https://www.ip2location.com/download/?token=upsMa90TPuFsGkgLFVEA66ZsARtKN9LFn6pfa7dn5BT7ZNL8mUJsHXS7KutlbVci&file=DB1');
    file_put_contents('../data/ip2location/IP2LOCATION-LITE-DB1.CSV', $contents);
}

function ip2locationLoad()
{
    echoFlush("Processing: ../data/ip2location/IP2LOCATION-LITE-DB1.CSV");
    if (($fp = fopen('../data/ip2location/IP2LOCATION-LITE-DB1.CSV', 'r')) !== false) {
        (new \DatabasesLocation\Ip2location())->createReplaceTableLikeMain();
        (new \DatabasesLocation\Ip2location())->truncateReplaceTable();
        $completedOld = '';
        while (!feof($fp)) {
            $completedNew = number_format((ftell($fp) / filesize('../data/ip2location/IP2LOCATION-LITE-DB1.CSV')) * 100, 1);
            if ($completedOld != $completedNew) {
                echoFlush("Completed: " . $completedNew . "%");
                $completedOld = $completedNew;
            }
            $buf = fgets($fp);
            if (!empty($buf)) {
                $work = explode('","', trim($buf, " \t\n\r\0\x0B\""));
                if (is_numeric($work[0])) {
                    (new \DatabasesLocation\Ip2location())->insertReplaceRow($work);
                }
            }
        }
        (new \DatabasesLocation\Ip2location())->swapReplaceIntoMain();
    }
}

function ip2locationFullDownload()
{
    $contents = file_get_contents('https://www.ip2location.com/download/?token=upsMa90TPuFsGkgLFVEA66ZsARtKN9LFn6pfa7dn5BT7ZNL8mUJsHXS7KutlbVci&file=DB1');
    file_put_contents('../data/ip2location/IP2LOCATION-LITE-DB1.CSV', $contents);
}

function ip2locationFullLoad()
{
    echoFlush("Processing: ../data/ip2location/IP2LOCATION-LITE-DB11.CSV");
    if (($fp = fopen('../data/ip2location/IP2LOCATION-LITE-DB11.CSV', 'r')) !== false) {
        (new \DatabasesLocation\Ip2locationFull())->createReplaceTableLikeMain();
        (new \DatabasesLocation\Ip2locationFull())->truncateReplaceTable();
        $completedOld = '';
        while (!feof($fp)) {
            $completedNew = number_format((ftell($fp) / filesize('../data/ip2location/IP2LOCATION-LITE-DB11.CSV')) * 100, 1);
            if ($completedOld != $completedNew) {
                echoFlush("Completed: " . $completedNew . "%");
                $completedOld = $completedNew;
            }
            $buf = fgets($fp);
            if (!empty($buf)) {
                $work = explode('","', trim($buf, " \t\n\r\0\x0B\""));
                if (is_numeric($work[0])) {
                    (new \DatabasesLocation\Ip2locationFull())->insertReplaceRow($work);
                }
            }
        }
        (new \DatabasesLocation\Ip2locationFull())->swapReplaceIntoMain();
    }
}

function echoFlush($text)
{
    echo $text . '<br />';
    flush();
}

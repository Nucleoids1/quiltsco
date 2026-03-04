<?php

function isSecurityCodeValid($securityCode)
{
    if ($GLOBALS['auth']['id']) {
        $securityCodeRow = (new \Databases\SecurityCodeLast())->findByUserId($GLOBALS['auth']['id']);
        if ($securityCodeRow) {
            (new \Databases\SecurityCodeLast())->deleteByUserId($GLOBALS['auth']['id']);
        }
    } else {
        $securityCodeCache = cookie('cache');
        $securityCodeRow = $securityCodeCache ? (new \Databases\SecurityCodeCache())->selectWhereRow(['cache' => $securityCodeCache]) : null;
        if ($securityCodeCache) {
            (new \Databases\SecurityCodeCache())->deleteWhere(['cache' => $securityCodeCache]);
        }
    }

    return $securityCodeRow && strtoupper($securityCode) == strtoupper($securityCodeRow['code']);
}

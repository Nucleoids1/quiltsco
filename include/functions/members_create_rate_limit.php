<?php
function membersCreateThrottleCheck(string $email, string $ip): bool
{
    (new \Databases\MembersCreate())->deleteOldPendingAccounts();

    if ($email && (new \Databases\MembersCreate())->countRecentByEmail($email, date('Y-m-d H:i:s', strtotime('-10 minutes')))) {
        return true;
    }

    if ($ip && (new \Databases\MembersCreate())->countRecentByIp($ip, date('Y-m-d H:i:s', strtotime('-10 minutes'))) >= 3) {
        return true;
    }

    return false;
}

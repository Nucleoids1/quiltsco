<?php
function ipMatchesSubnet($encodedIp1, $encodedIp2)
{
    if (intval($encodedIp1) === 0 || intval($encodedIp2) === 0) {
        return true;
    }

    $bits = defined('IP_BITS') ? intval(IP_BITS) : 14;
    if ($bits < 1) {
        $bits = 1;
    }
    if ($bits > 32) {
        $bits = 32;
    }

    $mask = $bits === 32 ? 0xFFFFFFFF : (~((1 << (32 - $bits)) - 1) & 0xFFFFFFFF);
    return (($encodedIp1 & $mask) === ($encodedIp2 & $mask));
}

function userAgentMatches($userAgent1, $userAgent2)
{
    if ($userAgent1 === '' || $userAgent2 === '') {
        return true;
    }

    if ($userAgent1 === $userAgent2) {
        return true;
    }

    $normalize = function ($ua) {
        $ua = preg_replace('/\/[\d.]+/', '', $ua);
        $ua = preg_replace('/\([^)]*\)/', '', $ua);
        $ua = preg_replace('/\s+/', ' ', trim($ua));
        return strtolower($ua);
    };

    return $normalize($userAgent1) === $normalize($userAgent2);
}

function verifyPassword($password, $storedHash)
{
    $hashInfo = password_get_info($storedHash);
    if (!empty($hashInfo['algo'])) {
        return password_verify($password, $storedHash);
    }

    return ($storedHash === md5(strtolower($password)));
}

function needsPasswordMigration($storedHash)
{
    $hashInfo = password_get_info($storedHash);
    return empty($hashInfo['algo']);
}

function needsPasswordRehash($storedHash)
{
    $hashInfo = password_get_info($storedHash);
    if (empty($hashInfo['algo'])) {
        return false;
    }

    return password_needs_rehash($storedHash, PASSWORD_DEFAULT);
}

function createSessionToken()
{
    return bin2hex(random_bytes(20));
}

function createUniqueIdentification()
{
    do {
        $sha1 = createSessionToken();
        $sha2 = createSessionToken();
        $existing = (new \Databases\MembersOnline())->findBySession($sha1, $sha2);
    } while ($existing);

    return [$sha1, $sha2];
}

function closeLogin($sha1, $sha2, $userId)
{
    $membersOnlineRow = (new \Databases\MembersOnline())->findLatestByUserOrSession($userId, $sha1, $sha2);

    if (!$membersOnlineRow) {
        return;
    }

    (new \Databases\MembersOnline())->closeSession($membersOnlineRow['sha1'], $membersOnlineRow['sha2']);
}

function updateMemberOnlineSession($sha1, $sha2)
{
    (new \Databases\MembersOnline())->updateSessionActivity(
        $sha1,
        $sha2,
        date('Y-m-d H:i:s', server('REQUEST_TIME')),
        ipEncode(server('REMOTE_ADDR')),
        substr(server('HTTP_USER_AGENT', ''), 0, 255)
    );
}

function createMemberOnlineSession($userId)
{
    list($sha1, $sha2) = createUniqueIdentification();
    (new \Databases\MembersOnline())->createSession(
        $userId,
        $sha1,
        $sha2,
        date('Y-m-d H:i:s'),
        ipEncode(server('REMOTE_ADDR')),
        substr(server('HTTP_USER_AGENT', ''), 0, 255)
    );
    makeCookie('sha1', $sha1, 1000);
    makeCookie('sha2', $sha2, 1000);
}

function removeMemberOnlineSession($sha1, $sha2, $userId, $clearCookies = false)
{
    if ($sha1 && $sha2) {
        closeLogin($sha1, $sha2, $userId);
        (new \Databases\MembersOnline())->deleteSession($sha1, $sha2);
    } else {
        (new \Databases\MembersOnline())->deleteByUserId($userId);
    }

    if ($clearCookies) {
        killCookie('sha1');
        killCookie('sha2');
    }
}

function processPostLogin()
{
    $loginEmail = post('login_email');
    $loginPassword = post('login_password');
    $loginAttemptMade = ($loginEmail && $loginPassword);

    if (!$loginAttemptMade) {
        return;
    }

    $membersRow = (new \Databases\Members())->findActiveByEmailOrUsername($loginEmail);

    if ($membersRow && verifyPassword($loginPassword, $membersRow['password'])) {
        if (needsPasswordMigration($membersRow['password']) || needsPasswordRehash($membersRow['password'])) {
            (new \Databases\Members())->updatePassword($membersRow['id'], password_hash($loginPassword, PASSWORD_DEFAULT));
        }

        createMemberOnlineSession($membersRow['id']);
        makeCookie('notice', 'Login Successful');
        header('Location: ./?' . server('QUERY_STRING'));
        die;
    }

    makeCookie('notice', 'Login Failed');
    header('Location: ./?' . server('QUERY_STRING'));
    die;
}

function loadAuthMember()
{
    $loggedIn = 0;

    $sha1 = cookie('sha1');
    $sha2 = cookie('sha2');
    if ($sha1 && $sha2) {
        $membersOnlineRow = (new \Databases\MembersOnline())->findBySession($sha1, $sha2);
        if ($membersOnlineRow) {
            $currentIp = ipEncode(server('REMOTE_ADDR'));
            $currentUserAgent = server('HTTP_USER_AGENT', '');

            $ipValid = ipMatchesSubnet($membersOnlineRow['ip'], $currentIp);
            $uaValid = userAgentMatches($membersOnlineRow['user_agent'], $currentUserAgent);

            if ($ipValid && $uaValid) {
                $membersRow = (new \Databases\Members())->findActiveById($membersOnlineRow['user_id']);
                if ($membersRow) {
                    $cutoff = date('Y-m-d H:i:s', time() - (30 * 60));
                    if ($membersOnlineRow['laston'] < $cutoff) {
                        updateMemberOnlineSession($membersOnlineRow['sha1'], $membersOnlineRow['sha2']);
                    } else {
                        (new \Databases\MembersOnline())->updateSessionActivityNow($membersOnlineRow['sha1'], $membersOnlineRow['sha2']);
                    }
                    $loggedIn = 1;
                }
            } else {
                error_log(sprintf(
                    'Session validation failed for user_id=%d, sha1=%s. IP match: %s, UA match: %s',
                    $membersOnlineRow['user_id'],
                    substr($sha1, 0, 8),
                    $ipValid ? 'yes' : 'no',
                    $uaValid ? 'yes' : 'no'
                ));
                removeMemberOnlineSession($sha1, $sha2, $membersOnlineRow['user_id'], false);
            }
        }
    }

    if ($loggedIn === 0) {
        processPostLogin();
        killCookie('sha1');
        killCookie('sha2');
        $membersRow = [
            'id' => 0,
            'email' => null,
            'password' => null,
        ];
    }

    return $membersRow;
}

function isPasswordSecure(string $password): bool
{
    return match (true) {
        strlen($password) < 8 => false,  // Minimum length
        !preg_match('/[A-Z]/', $password) => false,  // At least one uppercase
        !preg_match('/[a-z]/', $password) => false,  // At least one lowercase
        !preg_match('/[0-9]/', $password) => false,  // At least one digit
        !preg_match('/[^A-Za-z0-9]/', $password) => false,  // At least one special character
        default => true  // All checks passed
    };
}

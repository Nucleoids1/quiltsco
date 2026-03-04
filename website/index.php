<?php
    set_error_handler("myErrorHandler");
    function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $_requestUri = server('REQUEST_URI');
        switch ($errno) {
            case E_USER_ERROR:
                error_log("[E_USER_ERROR] " . $_requestUri . ": [" . $errno . "] " . $errstr . " LINE " . $errline . " IN " . $errfile);
                die;
            case E_USER_WARNING:
                error_log("[E_USER_WARNING] " . $_requestUri . ": [" . $errno . "] " . $errstr . " LINE " . $errline . " IN " . $errfile);
                die;
            case E_USER_NOTICE:
                error_log("[E_USER_NOTICE] " . $_requestUri . ": [" . $errno . "] " . $errstr . " LINE " . $errline . " IN " . $errfile);
                break;
            case E_DEPRECATED:
                error_log("[E_DEPRECATED] " . $_requestUri . ": [" . $errno . "] " . $errstr . " LINE " . $errline . " IN " . $errfile);
                break;
            default:
                error_log("[DEFAULT] " . $_requestUri . ": [" . $errno . "] " . $errstr . " LINE " . $errline . " IN " .$errfile);
                break;
        }
    }

    define('INDEX.PHP', 1);

    date_default_timezone_set('America/Montreal');

    require_once('../include/config.php');
    require_once('../include/functions/csrf.php');
    require_once('../include/functions/general.php');
    require_once('../include/functions/ip_decode.php');
    require_once('../include/functions/ip_encode.php');
    require_once('../include/functions/login.php');
    require_once('../include/functions/maxmind.php');
    require_once('../include/functions/output.php');

    if (hasForwardedHostHeader())
    {
        http_response_code(400);
        die;
    }

    spl_autoload_register(function ($class)
    {
        $prefixes = [
            'Databases\\' => '../include/classes/Databases/',
            'DatabasesSchemes\\' => '../include/classes/DatabasesSchemes/',
            'DatabasesLocation\\' => '../include/classes/DatabasesLocation/',
            'Connectors\\' => '../include/classes/Connectors/'
        ];

        foreach ($prefixes as $prefix => $baseDir)
        {
            if (strpos($class, $prefix) === 0)
            {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                if (is_file($file))
                {
                    require_once($file);
                }
                return;
            }
        }
    });

    $membersRow = loadAuthMember();

    if (cookie('login_email'))
    {
        killCookie('login_email');
        unset($_COOKIE['login_email']);
    }
    if (cookie('login_password'))
    {
        killCookie('login_password');
        unset($_COOKIE['login_password']);
    }

    $membersRow = setPermissions($membersRow);

    if ($membersRow['emulate'] && cookie('emulate'))
    {
        $members2Row = (new \Databases\Members())->selectWhereRow(['email' => cookie('emulate')]);
        if ($members2Row)
        {
               $membersRow = setPermissions($members2Row);
        }
       }

    $GLOBALS['auth'] = $membersRow;

    $_notice = cookie('notice');
    if (cookie('notice'))
    {
        killCookie('notice');
    }

    if ($GLOBALS['auth']['id'])
    {
        (new \Databases\MembersLaston())->replaceArray([
            'user_id' => $GLOBALS['auth']['id'],
        ]);
    }

    if ($GLOBALS['auth']['id'])
    {
        (new \Databases\StatsIps())->replaceArray([
            'user_id' => $GLOBALS['auth']['id'],
            'ip' => server('REMOTE_ADDR'),
            'country' => ip2country(),
        ]);
    }

    $_loadFileName = '';
    if (get('a'))
    {
        $_loadFileName = 'action/' . get('a');
    }
    elseif (get('aa'))
    {
        $_loadFileName = 'action/admin/' . get('aa');
    }
    elseif (get('am'))
    {
        $_loadFileName = 'action/member/' . get('am');
    }
    elseif (get('g'))
    {
        $_loadFileName = 'graphic/' . get('g');
    }
    elseif (get('j'))
    {
        $_loadFileName = 'ajax/' . get('j');
    }
    elseif (get('s'))
    {
        $_loadFileName = 'show/' . get('s');
    }
    elseif (get('sa'))
    {
        $_loadFileName = 'show/admin/' . get('sa');
    }
    elseif (get('sm'))
    {
        $_loadFileName = 'show/member/' . get('sm');
    }
    elseif (get('jm'))
    {
        $_loadFileName = 'ajax/member/' . get('jm');
    }
    elseif (get('ja'))
    {
        $_loadFileName = 'ajax/admin/' . get('ja');
    }

    elseif (get('t') && php_sapi_name() === 'cli')
    {
        $testFile = get('t');
        if (preg_match('/^(test|functions|show|action|graphic)_[a-z0-9_]+$/i', $testFile))
        {
            $_loadFileName = 'tests/' . $testFile;
        }
    }

    $include = '';
    if ($_loadFileName && preg_match('/^[a-z0-9_\/-]+$/i', $_loadFileName) && strpos($_loadFileName, '..') === false)
    {
        if (is_file('../include/' . $_loadFileName . '.php'))
        {
            if (strpos($_loadFileName, '/admin/') !== false && !$GLOBALS['auth']['sysop'])
            {
                //NOTHING
            }
            elseif (strpos($_loadFileName, '/member/') !== false && !$GLOBALS['auth']['id'])
            {
                //NOTHING
            }
            else
            {
                $include = '../include/' . $_loadFileName . '.php';
            }
        }
        else
        {
            $include = '../include/show/error.php';
        }
    }

    if ($include == '')
    {
        header('Location: ./?s=finished');
        die;
    }

    if ($_loadFileName && strpos($_loadFileName, 'action/') === 0)
    {
        csrfCheck();
    }

    if ($_loadFileName && strpos($_loadFileName, 'ajax/') === 0)
    {
        csrfCheck();
    }

    (new \Databases\TilesPending())->deleteExpired();

    if ($include)
    {
        include($include);
    }


       function setPermissions($user)
    {
        $user['sysop'] = false;
        $defaultPermsRows = (new \Databases\Permissions())->findAllDistinctPermissions();
        foreach ($defaultPermsRows as $defaultPermRow)
        {
            $user[$defaultPermRow['permission']] = false;
        }
        if ($user['id'])
        {
            $permissionsRows = (new \Databases\Permissions())->findByUserId($user['id']);
            foreach ($permissionsRows as $permissionRow)
            {
                if ($permissionRow['ip'] == '' || $permissionRow['ip'] == server('REMOTE_ADDR'))
                {
                    $user[$permissionRow['permission']] = true;
                    if ($user['sysop'] == false)
                    {
                        $user['sysop'] = true;
                    }
                }
            }
        }
        return $user;
    }

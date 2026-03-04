<?php
    require_once('../include/functions/image_upload.php');
    require_once('../include/functions/security_code.php');

    $_assignedTo = postInt('assigned_to');
    $_category = postInt('category');
    $_description = post('description');
    $_pictureDescription = post('picture_description');
    $_priority = postInt('priority');
    $_status = postInt('status');
    $_summary = post('summary');
    $_securityCode = post('security_code');
    $_redirectPath = './?s=tracker_bugs_admin' . ($_id ? '&i=' . $_id : '');


    if (!$_id && !$GLOBALS['auth']['id'] && !isSecurityCodeValid($_securityCode))
    {
        redirectWithNotice('The security code you entered was incorrect', $_redirectPath);
    }

    if (!trim($_summary))
    {
        redirectWithNotice('Summary is required', $_redirectPath);
    }

    if (!trim($_description))
    {
        redirectWithNotice('Description is required', $_redirectPath);
    }

    if ($_id)
    {
        $trackerBugsRow = (new \Databases\TrackerBugs())->findById($_id);
        if (!$trackerBugsRow)
        {
            $_id = null;
        }
    }

    if ($_id)
    {
        if ($GLOBALS['auth']['root'])
        {
            (new \Databases\TrackerBugs())->updateBug($_id, date('Y-m-d H:i:s', server('REQUEST_TIME')), $_category, $_priority, $_status, $_assignedTo, $_summary, $_description);
        }
    }
    else
    {
        $_id = (new \Databases\TrackerBugs())->createBug(
            $GLOBALS['auth']['id'],
            $_category,
            $_priority,
            $_status,
            $_assignedTo,
            $_summary,
            $_description
        );
    }

    $_picture = files('picture');
    if ($_picture)
    {
        if (!isset($_picture['name']) || !$_picture['name'] || !isset($_picture['tmp_name']) || !$_picture['tmp_name'])
        {
            redirectWithNotice('No File', './?s=tracker_bugs_admin&i=' . $_id);
        }

        list($new, $dupe, $notice) = processUploads();
        if ($new)
        {
            (new \Databases\TrackerBugsImages())->deleteByTrackerId($_id);
            (new \Databases\TrackerBugsImages())->attachImage(
                $new,
                $_id,
                $GLOBALS['auth']['id'],
                $_pictureDescription
            );
        }
        elseif ($dupe)
        {
            $trackerBugsImageRow = (new \Databases\TrackerBugsImages())->findOneByImageIdAndTrackerId($dupe, $_id);
            if (!$trackerBugsImageRow)
            {
                (new \Databases\TrackerBugsImages())->deleteByTrackerId($_id);
                (new \Databases\TrackerBugsImages())->attachImage($dupe, $_id, $GLOBALS['auth']['id'], $_pictureDescription);
            }
            else
            {
                $notice = 'Duplicate Image';
            }
        }
        makeCookie('notice', $notice);
    }

    header('Location: ./?s=tracker_bugs_admin&i=' . $_id);
    die;

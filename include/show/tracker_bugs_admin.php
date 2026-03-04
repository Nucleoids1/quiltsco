<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'home';

$trackerLanguage = language();
$trackerLanguageSuffix = $trackerLanguage === 'french' ? 'french' : 'english';

if ($_id) {
    $trackerBugsRow = (new \Databases\TrackerBugs())->findById($_id);
    if ($trackerBugsRow) {
        if ($GLOBALS['auth']['root']) {
            $trackerBugsCount = (new \Databases\TrackerBugs())->countFromId($_id);
            $countPage = ceil($trackerBugsCount / 50);
            $_page = $countPage;
        } else {
            $_id = null;
        }
    } else {
        $_id = null;
    }
}

$requiresAnonymousSecurityCode = (!$_id && !$GLOBALS['auth']['id']);
if ($requiresAnonymousSecurityCode && !cookie('cache')) {
    makeCookie('cache', makeCacheCode());
}

include('../include/parts/header.php');

$trackerBugsCount = (new \Databases\TrackerBugs())->countAll();
$countPage = ceil($trackerBugsCount / 50);
?>

<?php
echo boxOutsideTop('<a href="?s=tracker_bugs">Bug Tracker</a> - ' . ($_id ? 'Modify Bug' : 'Submit Bug'));
echo boxInsideTop();
?>
<form action="?a=tracker_bugs_admin<?php echo ($_id ? '&i=' . $_id : '') ?>&p=<?php echo $_page  ?>" enctype="multipart/form-data" method="post">
    <?php echo csrfField(); ?>
    <?php
    echo block('<label for="tracker_summary">Summary</label>', '<input name="summary" id="tracker_summary" value="' . ($_id ? safeAttr($trackerBugsRow['summary']) : '') . '" class="input_text" style="width: 300px;">');
    echo block('<label for="tracker_description">Description</label>', '<textarea name="description" id="tracker_description" cols="50" rows="16" class="input_text" style="width: 90%; height: 260px;">' . ($_id ? safeAttr($trackerBugsRow['description']) : '') . '</textarea>');
    $information = '<select name="category" id="tracker_category" class="input_select">';
    $trackerBugsCategoriesRows = (new \Databases\TrackerBugsCategories())->findAllOrderedByLanguage($trackerLanguageSuffix);
    foreach ($trackerBugsCategoriesRows as $trackerBugsCategoriesRow) {
        $information .= '<option value="' . $trackerBugsCategoriesRow['id'] . '"' . ($_id && $trackerBugsRow['category'] == $trackerBugsCategoriesRow['id'] ? ' selected' : '') . '>' . $trackerBugsCategoriesRow['category_' . $trackerLanguageSuffix] . '</option>';
    }
    $information .= '</select>';
    echo block('<label for="tracker_category">Category</label>', $information);
    if ($GLOBALS['auth']['root']) {
        $information = '<select name="assigned_to" id="tracker_assigned_to" class="input_select">';
        $information .= '<option value="0">Nobody</option>';
        $information .= '<option value="1"' . ($_id && $trackerBugsRow['assigned_to'] == 1 ? ' selected' : '') . '>' . getUsername(1) . '</option>';
        $information .= '</select>';
        echo block('<label for="tracker_assigned_to">Assigned To</label>', $information);
        $information = '<select name="status" id="tracker_status" class="input_select">';
        $trackerBugsStatusRows = (new \Databases\TrackerBugsStatus())->findAllOrderedById();
        foreach ($trackerBugsStatusRows as $trackerBugsStatusRow) {
            $information .= '<option value="' . $trackerBugsStatusRow['id'] . '"' . ($_id && $trackerBugsRow['status'] == $trackerBugsStatusRow['id'] ? ' selected' : '') . '>' . $trackerBugsStatusRow['status_' . $trackerLanguageSuffix] . '</option>';
        }
        $information .= '</select>';
        echo block('<label for="tracker_status">Status</label>', $information);
    }
    if ($_id) {
        $temp = '';
        $trackerBugsImagesRows = (new \Databases\TrackerBugsImages())->findByTrackerId($trackerBugsRow['id']);
        foreach ($trackerBugsImagesRows as $trackerBugsImagesRow) {
            $temp .= makeLink('?s=gallery_image&i=' . $trackerBugsImagesRow['image_id'], htmlentities($trackerBugsImagesRow['description'] ? $trackerBugsImagesRow['description'] : 'No Description'));
        }
        if ($temp) {
            echo block('Attached Images', $temp);
        }
    }
    echo block('<label for="tracker_picture">Attach File</label>', '<input type="file" name="picture" id="tracker_picture" class="input_text"> (optional)');
    echo block('<label for="tracker_picture_desc">Attach File Description</label>', '<input name="picture_description" id="tracker_picture_desc" class="input_text" style="width: 300px;"> (optional)');
    if ($requiresAnonymousSecurityCode) {
        echo block('', '<img src="?s=security_code&rand=' . random_int(intval('1' . str_repeat('0', strlen(strval(PHP_INT_MAX)) - 1)), PHP_INT_MAX) . '" alt="Security code - enter the characters shown" style="border: 0; vertical-align: middle;"> <label for="tracker_security_code">Security Code:</label> <input type="text" name="security_code" id="tracker_security_code" class="input_text" style="width: 100px;">');
    }
    echo block('', '<input type="submit" value="' . ($_id ? 'Modify Bug' : 'Add Bug') . '" class="input_submit">');
    ?>
</form>
<?php
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

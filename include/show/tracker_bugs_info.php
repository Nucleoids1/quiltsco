<?php
require_once('../include/functions/comments.php');

$GLOBALS['highlight'] = 'home';

$trackerLanguage = language();
$trackerLanguageSuffix = $trackerLanguage === 'french' ? 'french' : 'english';

$_id = getInt('i');


if ($_id) {
    $trackerBugsRow = (new \Databases\TrackerBugs())->findById($_id);
    if ($trackerBugsRow) {
        (new \Databases\TrackerBugs())->incrementViews($_id);
    } else {
        header('Location: ./?s=finished');
        die;
    }
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=tracker_bugs">Bug Tracker</a> - ' . $trackerBugsRow['summary'], ($GLOBALS['auth']['root'] ? '[ <a href="?s=tracker_bugs_admin&i=' . $trackerBugsRow['id'] . '">Modify</a> ]' : ''));
echo boxInsideTop();
echo block('Submited By', getUsername($trackerBugsRow['user_id'], 1));
echo block('Open Date', $trackerBugsRow['posted_on']);
$trackerBugsCategoriesRow = (new \Databases\TrackerBugsCategories())->findById($trackerBugsRow['category']);
if ($trackerBugsCategoriesRow) {
    echo block('Category', $trackerBugsCategoriesRow['category_' . $trackerLanguageSuffix]);
}
echo block('Summary', $trackerBugsRow['summary']);
echo block('Priority', $trackerBugsRow['priority']);
$trackerBugsStatusRow = (new \Databases\TrackerBugsStatus())->findById($trackerBugsRow['status']);
if ($trackerBugsStatusRow) {
    echo block('Status', '<span class="' . $trackerBugsStatusRow['class'] . '">' . $trackerBugsStatusRow['status_' . $trackerLanguageSuffix] . '</span>');
} else {
    echo block('Status', 'Unknown');
}
$open = 0;
if ($trackerBugsRow['status'] == 250) {
    $trackerBugsCommentsRow = (new \Databases\Comments('tracker_bugs_comments'))->findLastByLinkId($_id);
    if ($trackerBugsCommentsRow) {
        if ($trackerBugsCommentsRow['user_id'] == $GLOBALS['auth']['id']) {
            echo block('Reopen', makePostLink('?a=tracker_bugs_reopen&i=' . $_id, 'Click here to reopen this bug!'));
            $open = 1;
        }
    }
}
if ($open == 0) {
    echo block('Reopen', 'To reopen this closed bug please post a comment on why you would want it reopened and then an option to open it will appear.');
}
echo block('Description', nl2br(safeAttr($trackerBugsRow['description'])));
if ($trackerBugsRow['assigned_to']) {
    echo block('Assigned To', getUsername($trackerBugsRow['assigned_to'], 1));
}
$temp = '';
$trackerBugsImagesRows = (new \Databases\TrackerBugsImages())->findByTrackerId($trackerBugsRow['id']);
foreach ($trackerBugsImagesRows as $trackerBugsImagesRow) {
    $temp .= makeLink('?s=gallery_image&i=' . $trackerBugsImagesRow['image_id'], htmlentities($trackerBugsImagesRow['description'] ? $trackerBugsImagesRow['description'] : 'No Description'));
}
if ($temp) {
    echo block('Attached Images', $temp);
}
echo boxInsideBottom();
echo boxOutsideBottom();
?>

<?php
comments($_id, 'tracker_bugs_comments');

include('../include/parts/footer.php');

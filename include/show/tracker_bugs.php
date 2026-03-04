<?php
$GLOBALS['highlight'] = 'home';

$trackerLanguage = language();
$trackerLanguageSuffix = $trackerLanguage === 'french' ? 'french' : 'english';

$_category = getInt('category');
$_status = getInt('status');

$title = 'Bug Tracker';
include('../include/parts/header.php');

echo boxOutsideTop('Bug Tracker', '[ <a href="?s=tracker_bugs_admin">Submit Bug Report</a> ]');
echo boxInsideTop();
?>
<div style="text-align: right;">
    <form id="form_submit" action="?" method="get" class="form">
        Status:
        <input type="hidden" name="s" value="tracker_bugs">
        <select name="status" class="input_select" onChange="document.getElementById('form_submit').submit();">
            <option value="0">All Statuses</option>
            <?php
            $trackerBugsStatusRows = (new \Databases\TrackerBugsStatus())->findAllOrderedById();
            foreach ($trackerBugsStatusRows as $trackerBugsStatusRow) {
                echo '<option value="' . $trackerBugsStatusRow['id'] . '"' . ($trackerBugsStatusRow['id'] == $_status ? ' selected' : '') . '>' . $trackerBugsStatusRow['status_' . $trackerLanguageSuffix] . '</option>';
            }
            ?>
        </select>
        Category:
        <select name="category" class="input_select" onChange="document.getElementById('form_submit').submit();">
            <option value="0">All Categories</option>
            <?php
            $trackerBugsCategoriesRows = (new \Databases\TrackerBugsCategories())->findAllOrderedById();
            foreach ($trackerBugsCategoriesRows as $trackerBugsCategoriesRow) {
                echo '<option value="' . $trackerBugsCategoriesRow['id'] . '"' . ($trackerBugsCategoriesRow['id'] == $_category ? ' selected' : '') . '>' . $trackerBugsCategoriesRow['category_' . $trackerLanguageSuffix] . '</option>';
            }
            ?>
        </select>
    </form>
</div>
<?php
echo boxInsideBottom();
$trackerBugsRows = (new \Databases\TrackerBugs())->findByFilters($_status, $_category);
if ($trackerBugsRows) {
    echo boxInsideTop('margin: 10px 0px 0px 0px;');
    $z = 0;
    foreach ($trackerBugsRows as $trackerBugsRow) {
        echo '<div class="' . ($z++ % 2 ? 'on' : 'off') . '">';
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        echo '<td style="width: 50px;">' . $trackerBugsRow['id'] . '</td>';
        echo '<td>' . makeLink('?s=tracker_bugs_info&i=' . $trackerBugsRow['id'], $trackerBugsRow['summary']) . '</td>';
        $trackerBugsCategoriesRow = (new \Databases\TrackerBugsCategories())->findById($trackerBugsRow['category']);
        if ($trackerBugsCategoriesRow) {
            echo '<td style="width: 125px;">' . $trackerBugsCategoriesRow['category_' . $trackerLanguageSuffix] . '</td>';
        } else {
            echo '<td style="width: 125px;">Unknown</td>';
        }
        $trackerBugsStatusRow = (new \Databases\TrackerBugsStatus())->findById($trackerBugsRow['status']);
        if ($trackerBugsStatusRow) {
            echo '<td class="' . $trackerBugsStatusRow['class'] . '" style="width: 75px;">' . $trackerBugsStatusRow['status_' . $trackerLanguageSuffix] . '</td>';
        } else {
            echo '<td style="width: 75px;">Unknown</td>';
        }
        echo '<td style="width: 150px;">' . getUsername($trackerBugsRow['user_id'], 1) . '</td>';
        $trackerBugsCommentsCount = (new \Databases\Comments('tracker_bugs_comments'))->countByLinkId($trackerBugsRow['id']);
        echo '<td style="width: 100px;">' . $trackerBugsCommentsCount . ' Comments</td>';
        echo '<td style="width: 125px;">' . substr($trackerBugsRow['posted_on'], 0, 16) . '</td>';
        echo '</tr></table>';
        echo '</div>';
    }
    echo boxInsideBottom();
} else {
    echo '<div style="padding: 3px 0px 0px 0px;"></div>';
    echo boxInsideTop();
    echo 'There are no entries in the bugs database.';
    echo boxInsideBottom();
}
echo boxOutsideBottom();

include('../include/parts/footer.php');

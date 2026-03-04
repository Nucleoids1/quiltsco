<?php
require_once('../include/functions/community_permissions.php');

$GLOBALS['highlight'] = 'forum';

$_id = getInt('i');
$_adminName = get('admin_name');

$communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_id);
if (!$communityRow) {
    makeCookie('notice', 'Sorry, this section does not exist.');
    header('Location: ./?s=community_create');
    die;
}

communityPermissions($communityRow['community_id']);

if (!$GLOBALS['auth']['community']['administration']) {
    makeCookie('notice', 'Sorry, you don\'t have permission to modify this forum.');
    header('Location: ./?s=community_create');
    die;
}

if (!$userId = getUserId($_adminName)) {
    makeCookie('notice', 'Sorry, that user does not exist.');
    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;
}

$communitySectionsPermissionsRow = (new \Databases\CommunitySectionsPermissions())->findBySectionAndUser($communityRow['section_id'], $userId);
if (!$communitySectionsPermissionsRow) {
    makeCookie('notice', 'Sorry, that user is not a section moderator of your forum.');
    header('Location: ./?s=community_section_modify&i=' . $communityRow['section_id']);
    die;
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community_modify&i=' . $communityRow['community_id'] . '">Modify Your Community</a> - <a href="?s=community_section_modify&i=' . $communityRow['section_id'] . '">Modify Section</a> - ' . $_adminName . ' Moderator Info');
echo boxInsideTop();
?>

<form action="<?php echo safeUrl('?a=community_admin_modify_section&i=' . $communityRow['section_id'] . '&admin_name=' . $_adminName) ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    Granted Permissions
    <div style="padding: 5px 0px 0px 0px;"></div>
    <?php
    $communitySectionsPermissionsRows = (new \Databases\CommunitySectionsPermissions())->selectDistinctPermissions();
    foreach ($communitySectionsPermissionsRows as $communitySectionsPermissionsRow) {
        $hasPerm = (new \Databases\CommunitySectionsPermissions())->hasPermissionBySectionAndUser($communityRow['section_id'], $userId, $communitySectionsPermissionsRow['permission']);
        if ($communitySectionsPermissionsRow['permission'] != 'admin') {
            echo '<input type="checkbox" name="' . safeAttr($communitySectionsPermissionsRow['permission']) . '"' . ($hasPerm ? 'CHECKED' : '') . '> ' . safeAttr($communitySectionsPermissionsRow['permission']) . '<br />';
        }
    }
    ?>
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Modify Moderator Permissions" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

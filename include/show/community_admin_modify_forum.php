<?php
require_once('../include/functions/community_permissions.php');

$GLOBALS['highlight'] = 'forum';

$_id = getInt('i');
$_adminName = get('admin_name');

$communityRow = (new \Databases\CommunityForums())->findActiveForumContextInActiveSection($_id);
if (!$communityRow) {
    makeCookie('notice', 'Sorry, this forum does not exist.');
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
    header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
    die;
}

$communityForumsPermissionsRow = (new \Databases\CommunityForumsPermissions())->findByForumAndUser($communityRow['forum_id'], $userId);
if (!$communityForumsPermissionsRow) {
    makeCookie('notice', 'Sorry, that user is not an moderator of your forum.');
    header('Location: ./?s=community_forum_modify&i=' . $communityRow['forum_id']);
    die;
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community_modify&i=' . $communityRow['community_id'] . '">Modify Your Community</a> - <a href="?s=community_section_modify&i=' . $communityRow['section_id'] . '">Modify Section</a> - <a href="?s=community_forum_modify&i=' . $communityRow['forum_id'] . '">Modify Forum</a> - ' . $_adminName . ' Moderator Info');
echo boxInsideTop();
?>

<form action="<?php echo safeUrl('?a=community_admin_modify_forum&i=' . $communityRow['forum_id'] . '&admin_name=' . $_adminName) ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    Granted Permissions
    <div style="padding: 5px 0px 0px 0px;"></div>
    <?php
    $communityForumsPermissionsRows = (new \Databases\CommunityForumsPermissions())->selectDistinctPermissions();
    foreach ($communityForumsPermissionsRows as $communityForumsPermissionsRow) {
        $hasPerm = (new \Databases\CommunityForumsPermissions())->hasPermissionByForumAndUser($communityRow['forum_id'], $userId, $communityForumsPermissionsRow['permission']);
        if ($communityForumsPermissionsRow['permission'] != 'admin') {
            echo '<input type="checkbox" name="' . safeAttr($communityForumsPermissionsRow['permission']) . '"' . ($hasPerm ? 'CHECKED' : '') . '> ' . safeAttr($communityForumsPermissionsRow['permission']) . '<br />';
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

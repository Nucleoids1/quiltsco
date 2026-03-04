<?php
require_once('../include/functions/community_permissions.php');

$GLOBALS['highlight'] = 'forum';


$_id = getInt('i');
$_adminName = get('admin_name');

if (language() == 'french') {
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR', 'Sorry, that user is not a moderator of your community.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF', 'Sorry, but you can not edit your own permissions.');
} else {
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY', 'Sorry, but this community does not exist.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION', 'Sorry, you don\'t have permission to modify this community.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER', 'Sorry, that user does not exist.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR', 'Sorry, that user is not a moderator of your community.');
    define('ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF', 'Sorry, but you can not edit your own permissions.');
}

$communityRow = (new \Databases\Community())->findById($_id);
if (!$communityRow) {
    makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_COMMUNITY);
    header('Location: ./?s=community_create');
    die;
}

communityPermissions($communityRow['community_id']);

if (!$GLOBALS['auth']['community']['administration']) {
    makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_PERMISSION);
    header('Location: ./?s=community_create');
    die;
}

if (!$userId = getUserId($_adminName)) {
    makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_USER);
    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;
}

if ($userId == $GLOBALS['auth']['id']) {
    makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_YOURSELF);
    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;
}

$communityPermissionsRow = (new \Databases\CommunityPermissions())->findByCommunityAndUser($communityRow['community_id'], $userId);
if (!$communityPermissionsRow) {
    makeCookie('notice', ACTION_COMMUNITY_ADMIN_NOT_FOUND_MODERATOR);
    header('Location: ./?s=community_modify&i=' . $communityRow['community_id']);
    die;
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community_modify&i=' . $communityRow['community_id'] . '">Modify Your Community</a> - ' . $_adminName . ' Moderator Info');
echo boxInsideTop();
?>

<form action="<?php echo safeUrl('?a=community_admin_modify&i=' . $communityRow['community_id'] . '&admin_name=' . $_adminName) ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    Granted Permissions
    <div style="padding: 5px 0px 0px 0px;"></div>
    <?php
    $communityPermissionsRows = (new \Databases\CommunityPermissions())->selectDistinctPermissionsExcept(['admin', 'administrator']);
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        $hasPerm = (new \Databases\CommunityPermissions())->hasPermissionByCommunityAndUser($communityRow['community_id'], $userId, $communityPermissionsRow['permission']);
        echo '<input type="checkbox" name="' . safeAttr($communityPermissionsRow['permission']) . '"' . ($hasPerm ? 'CHECKED' : '') . '> ' . safeAttr($communityPermissionsRow['permission']) . '<br />';
    }
    ?>
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Modify Moderator Permissions" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

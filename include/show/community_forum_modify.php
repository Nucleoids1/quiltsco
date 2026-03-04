<?php
require_once('../include/functions/community_permissions.php');

$GLOBALS['highlight'] = 'forum';

$_id = get('i');

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

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community_modify&i=' . $communityRow['community_id'] . '">Modify Your Community</a> - <a href="?s=community_section_modify&i=' . $communityRow['section_id'] . '">Modify Section</a> - Modify Forum');
echo boxInsideTop();
?>

<form action="<?php echo safeUrl('?a=community_forum_modify&i=' . $communityRow['forum_id']); ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    <label for="forum_modify_name"><b>Forum Name</b></label> (<?php echo COMMUNITY_FORUM_NAME_MIN ?> to <?php echo COMMUNITY_FORUM_NAME_MAX ?> characters in length.)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="name" id="forum_modify_name" maxlength="<?php echo COMMUNITY_FORUM_NAME_MAX; ?>" value="<?php echo htmlentities($communityRow['forum_name_english']); ?>" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <label for="forum_modify_description"><b>Forum Description</b></label> (<?php echo COMMUNITY_FORUM_DESC_MIN ?> to <?php echo COMMUNITY_FORUM_DESC_MAX ?> characters in length.)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="description" id="forum_modify_description" maxlength="<?php echo COMMUNITY_FORUM_DESC_MAX; ?>" value="<?php echo htmlentities($communityRow['forum_description_english']) ?>" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Modify Forum" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

echo boxOutsideTop('Forum Moderators');
echo boxInsideTop();

$i = 0;
$communityForumsPermissionsRows = (new \Databases\CommunityForumsPermissions())->selectDistinctUserIdsByForum($communityRow['forum_id']);
if (count($communityForumsPermissionsRows)) {
    foreach ($communityForumsPermissionsRows as $communityForumsPermissionsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo '[' . $communityForumsPermissionsRow['user_id'] . '] <span style="font-size: 1.05rem; font-weight: bold;">';
        echo makeLink(safeUrl('?s=community_admin_modify_forum&i=' . $communityRow['forum_id'] . '&admin_name=' . getUsername($communityForumsPermissionsRow['user_id'])), safeAttr(getUsername($communityForumsPermissionsRow['user_id']))) . '</span>';
        echo ' [' . makePostLink('?a=community_admin_delete_forum&i=' . $communityRow['forum_id'] . '&admin_name=' . getUsername($communityForumsPermissionsRow['user_id']), 'remove', 'Are you sure you want to remove this admin?') . ']';
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        $j = 0;
        $communityForumsPermissionsUserRows = (new \Databases\CommunityForumsPermissions())->selectByForumAndUser($communityRow['forum_id'], $communityForumsPermissionsRow['user_id']);
        foreach ($communityForumsPermissionsUserRows as $communityForumsPermissionsUserRow) {
            if ($j++) {
                echo ', ';
            }
            echo $communityForumsPermissionsUserRow['permission'];
        }
    }
} else {
    echo 'There are no forum moderators.';
}

echo boxInsideBottom();
echo '<div style="padding: 5px 0px 0px 0px;"></div>';
echo boxInsideTop();
?>

<form action="<?php echo safeUrl('?a=community_admin_add_forum&i=' . $communityRow['forum_id']) ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    <label for="forum_modify_admin_name"><b>Add A Moderator</b></label> (enter username)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="admin_name" id="forum_modify_admin_name" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Add Moderator" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

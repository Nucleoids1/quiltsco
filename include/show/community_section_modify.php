<?php
require_once('../include/functions/community_permissions.php');

//SAFE SHOW

$GLOBALS['highlight'] = 'forum';


$_id = getInt('i');

$communityRow = (new \Databases\CommunitySections())->findActiveSectionContext($_id);
if (!$communityRow) {
    makeCookie('notice', 'Sorry, but what you are trying to access does not exist.');
    header('Location: ./?s=community_create');
    die;
}

communityPermissions($communityRow['community_id']);

if (!$GLOBALS['auth']['community']['administration']) {
    makeCookie('notice', 'Sorry, you don\'t have permission to modify this community.');
    header('Location: ./?s=community_create');
    die;
}

include('../include/parts/header.php');

echo boxOutsideTop('<a href="?s=community_modify&i=' . $communityRow['community_id'] . '">Modify Your Community</a> - Modify Section');
echo boxInsideTop();
?>

<form action="?a=community_section_modify&i=<?php echo $communityRow['section_id'] ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    <label for="section_modify_name"><b>Section Name</b></label> (<?php echo COMMUNITY_SECTION_NAME_MIN ?> to <?php echo COMMUNITY_SECTION_NAME_MAX ?> characters in length.)
    <br />
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="name" id="section_modify_name" maxlength="<?php echo COMMUNITY_SECTION_NAME_MAX ?>" class="input_text" value="<?php echo safeAttr($communityRow['section_name_english']) ?>" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Modfify Section" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

echo boxOutsideTop('Forums');
echo boxInsideTop();

$i = 0;
$communityForumsRows = (new \Databases\CommunityForums())->selectActiveBySectionOrdered($communityRow['section_id']);
if ($forumCount = count($communityForumsRows)) {
    foreach ($communityForumsRows as $communityForumsRow) {
        if ($i > 0) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo '<div class="' . ($i % 2 ? 'on' : 'off') . '">';
        echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
        echo '<td>';
        echo '<div style="font-size: 1.05rem; font-weight: bold">' . makeLink('?s=community_forum_modify&i=' . $communityForumsRow['forum_id'], safeAttr($communityForumsRow['forum_name_english']));
        echo ' [' . makePostLink('?a=community_forum_delete&i=' . $communityForumsRow['forum_id'], 'Remove', 'Are you sure you want to delete this forum?') . ']';
        echo '</div>';
        echo '<div style="font-size: 1.0rem;">' . safeAttr($communityForumsRow['forum_description_english']) . '</div>';
        echo '</td>';
        if ($i) {
            echo '<td style="width: 75px;">' . makePostLink('?a=community_forum_up&i=' . $communityForumsRow['forum_id'], 'Move Up') . '</td>';
        } else {
            echo '<td style="width: 75px; white-space: nowrap;"></td>';
        }
        if ($i < $forumCount - 1) {
            echo '<td style="width: 75px;">' . makePostLink('?a=community_forum_down&i=' . $communityForumsRow['forum_id'], 'Move Down') . '</td>';
        } else {
            echo '<td style="width: 75px; white-space: nowrap;"></td>';
        }
        echo '</tr></table>';
        echo '</div>';
        $i++;
    }
} else {
    echo 'No forums exist in this section.';
}

echo boxInsideBottom();
echo '<div style="padding: 5px 0px 0px 0px;"></div>';
echo boxInsideTop();
?>

<form action="?a=community_forum_add&i=<?php echo $communityRow['section_id'] ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    <label for="section_modify_forum_name"><b>Add A Forum</b></label> (<?php echo COMMUNITY_FORUM_NAME_MIN ?> to <?php echo COMMUNITY_FORUM_NAME_MAX ?> characters in length.)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="name" id="section_modify_forum_name" maxlength="<?php echo COMMUNITY_FORUM_NAME_MAX; ?>" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <label for="section_modify_forum_description"><b>Forum Description</b></label> (<?php echo COMMUNITY_FORUM_DESC_MIN ?> to <?php echo COMMUNITY_FORUM_DESC_MAX ?> characters in length.)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="description" id="section_modify_forum_description" maxlength="<?php echo COMMUNITY_FORUM_DESC_MAX; ?>" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Add Forum" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

echo boxOutsideTop('Section Moderators');
echo boxInsideTop();

$i = 0;
$communitySectionsPermissionsRows = (new \Databases\CommunitySectionsPermissions())->selectDistinctUserIdsBySection($communityRow['section_id']);
if (count($communitySectionsPermissionsRows)) {
    foreach ($communitySectionsPermissionsRows as $communitySectionsPermissionsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo '[' . $communitySectionsPermissionsRow['user_id'] . '] <span style="font-size: 1.05rem; font-weight: bold;">';
        echo makeLink('?s=community_admin_modify_section&i=' . $communityRow['section_id'] . '&admin_name=' . getUsername($communitySectionsPermissionsRow['user_id']), getUsername($communitySectionsPermissionsRow['user_id'])) . '</span>';
        echo ' [' . makePostLink('?a=community_admin_delete_section&i=' . $communityRow['section_id'] . '&admin_name=' . getUsername($communitySectionsPermissionsRow['user_id']), 'remove', 'Are you sure you want to remove this admin?') . ']';
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        $j = 0;
        $communitySectionsPermissionsUserRows = (new \Databases\CommunitySectionsPermissions())->selectBySectionAndUser($communityRow['section_id'], $communitySectionsPermissionsRow['user_id']);
        foreach ($communitySectionsPermissionsUserRows as $communitySectionsPermissionsUserRow) {
            if ($j++) {
                echo ', ';
            }
            echo $communitySectionsPermissionsUserRow['permission'];
        }
    }
} else {
    echo 'There are no section moderators.';
}

echo boxInsideBottom();
echo '<div style="padding: 5px 0px 0px 0px;"></div>';
echo boxInsideTop();
?>

<form action="?a=community_admin_add_section&i=<?php echo $communityRow['section_id'] ?>" method="post" class="form">
    <?php echo csrfField(); ?>
    <label for="section_modify_admin_name"><b>Add A Moderator</b></label> (enter username)
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="text" name="admin_name" id="section_modify_admin_name" class="input_text" style="width: 400px;">
    <div style="padding: 5px 0px 0px 0px;"></div>
    <input type="submit" value="Add Moderator" class="input_submit">
</form>

<?php
echo boxInsideBottom();
echo boxOutsideBottom();

include('../include/parts/footer.php');

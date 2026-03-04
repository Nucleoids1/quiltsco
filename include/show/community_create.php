<?php
$GLOBALS['highlight'] = 'forum';

//SAFE SHOW

include('../include/parts/header.php');

$communityPermissionsRows = (new \Databases\CommunityPermissions())->selectAdministratorCommunitiesByUser($GLOBALS['auth']['id']);

if (count($communityPermissionsRows) < 5) {
    echo boxOutsideTop('Create A Community');
    echo boxInsideTop();
?>

    <form action="?a=community_create" method="post" class="form">
        <?php echo csrfField(); ?>
        <label for="community_create_name"><b>Community Name</b></label> (<?php echo COMMUNITY_NAME_MIN ?> to <?php echo COMMUNITY_NAME_MAX ?> characters in length. No spaces.)
        <br />
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="text" name="name" id="community_create_name" maxlength="<?php echo COMMUNITY_NAME_MAX ?>" class="input_text" style="width: 400px;">
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Create Community" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if ($communityPermissionsRows) {
    echo boxOutsideTop('Active Communities');
    echo boxInsideTop();
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        echo makeLink('?s=community_modify&i=' . $communityPermissionsRow['community_id'], htmlentities($communityPermissionsRow['community_name'])) . '<br />';
    }
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

include('../include/parts/footer.php');

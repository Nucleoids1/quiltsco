<?php
$GLOBALS['highlight'] = 'quilts_moderate';

$_id = getInt('i');

include('../include/parts/header.php');

$quiltsRow = (new \Databases\Quilts())->findById($_id);
if ($quiltsRow) {
    if ($quiltsRow['finished']) {
        header('Location: ./?s=quilts_moderate');
        die;
    }

    $quiltsPermissionsCount = (new \Databases\QuiltsPermissions())->countRootByUserAndQuilt($GLOBALS['auth']['id'], $_id);
    if (!$quiltsPermissionsCount) {
        header('Location: ./?s=quilts_moderate');
        die;
    }
} else {
    header('Location: ./?s=quilts_moderate');
    die;
}
?>

<h1 class="header"><?php echo makeLink('?s=quilts_moderate', 'Moderate Quilts') ?> - Modify An Existing Quilt</h1>

<div class="content">
    <?php
    if (isset($errors)) {
        echo '<span style="color: red;">' . $errors . '</span>';
        echo '<div style="padding: 10px 0px 0px 0px;"></div>';
    }
    ?>
    <form action="?a=quilt_edit&i=<?php echo $_id ?>" method="post" style="margin: 0px; display: inline;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_name">Quilt Name:</label></td>
                <td><input name="name" id="quilt_edit_name" value="<?php echo (isset($_name) ? safeAttr(stripslashes($quiltsRow['name'])) : safeAttr($quiltsRow['name'])) ?>" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_description">Quilt Description:</label></td>
                <td><textarea name="description" id="quilt_edit_description" class="input_text" style="width: 400px; height: 100px;"><?php echo (isset($_description) ? safeAttr(stripslashes($_description)) : safeAttr($quiltsRow['description'])) ?></textarea></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_timelimit">Hours To Complete:</label></td>
                <td><input name="timelimit" id="quilt_edit_timelimit" value="<?php echo intval($quiltsRow['timelimit'] / 60 / 60) ?>" class="input_text" style="width: 40px;"> (1 to 72)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_multiple">Choose Multiple Tiles:</label></td>
                <td><select class="input_select" name="multiple" id="quilt_edit_multiple">
                        <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo '<option value="' . $i . '"' . ($quiltsRow['multiple'] == $i ? ' selected' : '') . '>' . $i . '</option>' . "\r\n";
                        }
                        ?>
                    </select> Allow user to checkout multiple tiles.
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_side_pixels">Side Pixels To Display:</label></td>
                <td><select class="input_select" name="side_pixels" id="quilt_edit_side_pixels">
                        <option value="15" <?php echo ($quiltsRow['side_pixels'] == 15) ? ' selected' : '' ?>>15</option>
                        <option value="16" <?php echo ($quiltsRow['side_pixels'] == 16) ? ' selected' : '' ?>>16</option>
                        <option value="20" <?php echo ($quiltsRow['side_pixels'] == 20) ? ' selected' : '' ?>>20</option>
                        <option value="24" <?php echo ($quiltsRow['side_pixels'] == 24) ? ' selected' : '' ?>>24</option>
                        <option value="25" <?php echo ($quiltsRow['side_pixels'] == 25) ? ' selected' : '' ?>>25</option>
                        <option value="30" <?php echo ($quiltsRow['side_pixels'] == 30) ? ' selected' : '' ?>>30</option>
                        <option value="32" <?php echo ($quiltsRow['side_pixels'] == 32) ? ' selected' : '' ?>>32</option>
                        <option value="35" <?php echo ($quiltsRow['side_pixels'] == 35) ? ' selected' : '' ?>>35</option>
                        <option value="40" <?php echo ($quiltsRow['side_pixels'] == 40) ? ' selected' : '' ?>>40</option>
                        <option value="45" <?php echo ($quiltsRow['side_pixels'] == 45) ? ' selected' : '' ?>>45</option>
                        <option value="50" <?php echo ($quiltsRow['side_pixels'] == 50) ? ' selected' : '' ?>>50</option>
                    </select> Pixels user has on the checkout tile to work with.
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_level">Quilt Level:</label></td>
                <td><select class="input_select" name="level" id="quilt_edit_level">
                        <option value="0" <?php echo ($quiltsRow['level'] == 0) ? ' selected' : '' ?>>Beginner</option>
                        <option value="1" <?php echo ($quiltsRow['level'] == 1) ? ' selected' : '' ?>>Intermediate</option>
                        <option value="2" <?php echo ($quiltsRow['level'] == 2) ? ' selected' : '' ?>>Advanced</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_photographs_allowed">Photographs Allowed:</label></td>
                <td><select class="input_select" name="photographs_allowed" id="quilt_edit_photographs_allowed">
                        <option value="0" <?php echo ($quiltsRow['photographs_allowed'] == 0) ? ' selected' : '' ?>>No</option>
                        <option value="1" <?php echo ($quiltsRow['photographs_allowed'] == 1) ? ' selected' : '' ?>>Yes</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_show_all">Tiles Visible:</label></td>
                <td><select class="input_select" name="show_all" id="quilt_edit_show_all">
                        <option value="0" <?php echo ($quiltsRow['show_all'] == 0) ? ' selected' : '' ?>>When Neighbours Are Finished</option>
                        <option value="1" <?php echo ($quiltsRow['show_all'] == 1) ? ' selected' : '' ?>>Instantly</option>
                        <option value="2" <?php echo ($quiltsRow['show_all'] == 2) ? ' selected' : '' ?>>When Whole Quilt Is Complete</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_work_on_all">Can Work On Tiles:</label></td>
                <td><select class="input_select" name="work_on_all" id="quilt_edit_work_on_all">
                        <option value="0" <?php echo ($quiltsRow['work_on_all'] == 0) ? ' selected' : '' ?>>Not Next To Your Own</option>
                        <option value="1" <?php echo ($quiltsRow['work_on_all'] == 1) ? ' selected' : '' ?>>Even Ones Next To Your Own</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_start_anywhere">Checkout Tile:</label></td>
                <td><select class="input_select" name="start_anywhere" id="quilt_edit_start_anywhere">
                        <option value="0" <?php echo ($quiltsRow['start_anywhere'] == 0) ? ' selected' : '' ?>>Only Next To An Existing Tile</option>
                        <option value="1" <?php echo ($quiltsRow['start_anywhere'] == 1) ? ' selected' : '' ?>>Anywhere Thats Available</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_moderated">Moderated:</label></td>
                <td><select class="input_select" name="moderated" id="quilt_edit_moderated">
                        <option value="0" <?php echo ($quiltsRow['moderated'] == 0) ? ' selected' : '' ?>>No</option>
                        <option value="1" <?php echo ($quiltsRow['moderated'] == 1) ? ' selected' : '' ?>>Yes</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Edit Quilt Properties" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<h2 class="header">Moderators</h2>

<div class="content">
    <form action="?a=quilt_moderator_add&i=<?php echo $_id ?>" method="post" style="margin: 0px; display: inline;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_add_moderator_name">Moderator:</label></td>
                <td><input name="name" id="quilt_edit_add_moderator_name" class="input_text"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Add Moderator" class="input_submit"></td>
            </tr>
        </table>
    </form>
    <?php
    $quiltsPermissionsRows = (new \Databases\QuiltsPermissions())->selectWithMembersByQuilt($_id);
    if (count($quiltsPermissionsRows)) {
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        echo '<div class="patch_fill">';
        foreach ($quiltsPermissionsRows as $quiltsPermissionsRow) {
            echo $quiltsPermissionsRow['permission'] == 'root' ? '[ <span class="error">Remove</span> ] ' : '[ ' . makePostLink('?a=quilt_moderator_remove&i=' . $_id . '&u=' . $quiltsPermissionsRow['username'], 'Remove', 'Remove moderator?') . ' ] ';
            echo $quiltsPermissionsRow['permission'] == 'root' ? '[ <span style="color: green;">ROOT</span> ] [ MODERATOR ] ' : ' [ ROOT ] [ <span style="color: green;">MODERATOR</span> ] ';
            echo makeLink('?s=profile&u=' . $quiltsPermissionsRow['username'], $quiltsPermissionsRow['username']) . '<br />';
        }
        echo '</div>';
    }
    ?>
</div>

<h2 class="header">Members Allowed (Not Working Yet)</h2>

<div class="content">
    <form action="?a=quilt_invites_add&i=<?php echo $_id ?>" method="post" style="margin: 0px; display: inline;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="quilt_edit_add_user_name">Username:</label></td>
                <td><input name="name" id="quilt_edit_add_user_name" class="input_text"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Add User" class="input_submit"></td>
            </tr>
        </table>
    </form>
    <div style="padding: 5px 0px 0px 0px;"></div>
    <div class="patch_fill">
        <?php
        $quiltsInvitesRows = (new \Databases\QuiltsInvites())->selectWithMembersByQuilt($_id);
        if (count($quiltsInvitesRows)) {
            foreach ($quiltsInvitesRows as $quiltsInvitesRow) {
                echo '[ ' . makeLink('?a=quilt_invites_remove&i=' . $_id . '&u=' . $quiltsInvitesRow['username'], 'Remove') . ' ] ';
                echo $quiltsInvitesRow['active'] ? '[ <span style="color: green;">Active</span> ] [ Pending ] ' : ' [ Active ] [ <span style="color: green;">Pending</span> ] [ ' . makeLink('?a=quilt_invites_remove&i=' . $_id . '&u=' . $quiltsInvitesRow['username'], 'Accept User') . ' ]';
                echo makeLink('?s=profile&u=' . $quiltsInvitesRow['username'], $quiltsInvitesRow['username']) . '<br />';
            }
        } else {
            echo 'All members are allowed to work on this quilt.';
        }
        ?>
    </div>
</div>

<?php
include('../include/parts/footer.php');

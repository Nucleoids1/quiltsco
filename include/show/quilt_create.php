<?php
$GLOBALS['highlight'] = 'userinfo';

include('../include/parts/header.php');
?>

<h1 class="header"><?php echo makeLink('?s=quilts_moderate', 'Moderate Quilts') ?> - Create A New Quilt</h1>

<div class="content">
    <?php
    if (isset($errors)) {
        echo '<span style="color: red;">' . $errors . '</span>';
        echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    }
    ?>
    <form action="?a=quilt_create" method="post" style="margin: 0px; display: inline;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="quilt_create_name">Quilt Name:</label></td>
                <td><input name="name" id="quilt_create_name" value="<?php echo isset($_name) ? safeAttr(stripslashes($_name)) : '' ?>" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_description">Quilt Description:</label></td>
                <td><textarea name="description" id="quilt_create_description" class="input_text" style="width: 400px; height: 100px;"><?php echo isset($_description) ? safeAttr(stripslashes($_description)) : '' ?></textarea></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_tile_width">Tile Width:</label></td>
                <td><input name="tile_width" id="quilt_create_tile_width" value="<?php echo isset($_tileWidth) ? $_tileWidth : '' ?>" class="input_text" style="width: 40px;"> (100 to 800)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_tile_height">Tile Height:</label></td>
                <td><input name="tile_height" id="quilt_create_tile_height" value="<?php echo isset($_tileHeight) ? $_tileHeight : '' ?>" class="input_text" style="width: 40px;"> (100 to 800)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_quilt_width">Quilt Width:</label></td>
                <td><input name="quilt_width" id="quilt_create_quilt_width" value="<?php echo isset($_quiltWidth) ? $_quiltWidth : '' ?>" class="input_text" style="width: 40px;"> (1 to 15)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_quilt_height">Quilt Height:</label></td>
                <td><input name="quilt_height" id="quilt_create_quilt_height" value="<?php echo isset($_quiltHeight) ? $_quiltHeight : '' ?>" class="input_text" style="width: 40px;"> (1 to 15)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_timelimit">Hours To Complete:</label></td>
                <td><input name="timelimit" id="quilt_create_timelimit" value="<?php echo isset($_timeLimit) ? $_timeLimit : '' ?>" class="input_text" style="width: 40px;"> (1 to 72)</td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_multiple">Choose Multiple Tiles:</label></td>
                <td><select class="input_select" name="multiple" id="quilt_create_multiple">
                        <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo '<option value="' . $i . '"' . (isset($_multiple) && $_multiple == $i ? ' selected' : '') . '>' . $i . '</option>' . "\r\n";
                        }
                        ?>
                    </select> Allow user to checkout multiple tiles.
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_side_pixels">Side Pixels To Display:</label></td>
                <td><select class="input_select" name="side_pixels" id="quilt_create_side_pixels">
                        <option value="15" <?php echo (isset($_sidePixels) && $_sidePixels == 15) ? ' selected' : '' ?>>15</option>
                        <option value="16" <?php echo (isset($_sidePixels) && $_sidePixels == 16) ? ' selected' : '' ?>>16</option>
                        <option value="20" <?php echo (isset($_sidePixels) && $_sidePixels == 20) ? ' selected' : '' ?>>20</option>
                        <option value="24" <?php echo (isset($_sidePixels) && $_sidePixels == 24) ? ' selected' : '' ?>>24</option>
                        <option value="25" <?php echo (isset($_sidePixels) && $_sidePixels == 25) ? ' selected' : '' ?>>25</option>
                        <option value="30" <?php echo (isset($_sidePixels) && $_sidePixels == 30) ? ' selected' : '' ?>>30</option>
                        <option value="32" <?php echo (isset($_sidePixels) && $_sidePixels == 32) ? ' selected' : '' ?>>32</option>
                        <option value="35" <?php echo (isset($_sidePixels) && $_sidePixels == 35) ? ' selected' : '' ?>>35</option>
                        <option value="40" <?php echo (isset($_sidePixels) && $_sidePixels == 40) ? ' selected' : '' ?>>40</option>
                        <option value="45" <?php echo (isset($_sidePixels) && $_sidePixels == 45) ? ' selected' : '' ?>>45</option>
                        <option value="50" <?php echo (isset($_sidePixels) && $_sidePixels == 50) ? ' selected' : '' ?>>50</option>
                    </select> Pixels user has on the checkout tile to work with.
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_level">Quilt Level:</label></td>
                <td><select class="input_select" name="level" id="quilt_create_level">
                        <option value="0" <?php echo (isset($_level) && $_level == 0) ? ' selected' : '' ?>>Beginner</option>
                        <option value="1" <?php echo (isset($_level) && $_level == 1) ? ' selected' : '' ?>>Intermediate</option>
                        <option value="2" <?php echo (isset($_level) && $_level == 2) ? ' selected' : '' ?>>Advanced</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_photographs_allowed">Photographs Allowed:</label></td>
                <td><select class="input_select" name="photographs_allowed" id="quilt_create_photographs_allowed">
                        <option value="0" <?php echo (isset($_photographsAllowed) && $_photographsAllowed == 0) ? ' selected' : '' ?>>No</option>
                        <option value="1" <?php echo (isset($_photographsAllowed) && $_photographsAllowed == 1) ? ' selected' : '' ?>>Yes</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_edge_wrap">Edge Wrap:</label></td>
                <td><select class="input_select" name="edge_wrap" id="quilt_create_edge_wrap">
                        <option value="0" <?php echo (isset($_edgeWrap) && $_edgeWrap == 0) ? ' selected' : '' ?>>None</option>
                        <option value="1" <?php echo (isset($_edgeWrap) && $_edgeWrap == 1) ? ' selected' : '' ?>>Horizonal</option>
                        <option value="2" <?php echo (isset($_edgeWrap) && $_edgeWrap == 2) ? ' selected' : '' ?>>Verticle</option>
                        <option value="3" <?php echo (isset($_edgeWrap) && $_edgeWrap == 3) ? ' selected' : '' ?>>All Edges</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_show_all">Tiles Visible:</label></td>
                <td><select class="input_select" name="show_all" id="quilt_create_show_all">
                        <option value="0" <?php echo (isset($_showAll) && $_showAll == 0) ? ' selected' : '' ?>>When Neighbours Are Finished</option>
                        <option value="1" <?php echo (isset($_showAll) && $_showAll == 1) ? ' selected' : '' ?>>Instantly</option>
                        <option value="2" <?php echo (isset($_showAll) && $_showAll == 2) ? ' selected' : '' ?>>When Whole Quilt Is Complete</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_work_on_all">Can Work On Tiles:</label></td>
                <td><select class="input_select" name="work_on_all" id="quilt_create_work_on_all">
                        <option value="0" <?php echo (isset($_workOnAll) && $_workOnAll == 0) ? ' selected' : '' ?>>Not Next To Your Own</option>
                        <option value="1" <?php echo (isset($_workOnAll) && $_workOnAll == 1) ? ' selected' : '' ?>>Even Ones Next To Your Own</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_start_anywhere">Checkout Tile:</label></td>
                <td><select class="input_select" name="start_anywhere" id="quilt_create_start_anywhere">
                        <option value="0" <?php echo (isset($_startAnywhere) && $_startAnywhere == 0) ? ' selected' : '' ?>>Only Next To An Existing Tile</option>
                        <option value="1" <?php echo (isset($_startAnywhere) && $_startAnywhere == 1) ? ' selected' : '' ?>>Anywhere Thats Available</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="quilt_create_moderated">Moderated:</label></td>
                <td><select class="input_select" name="moderated" id="quilt_create_moderated">
                        <option value="0" <?php echo (isset($_moderated) && $_moderated == 0) ? ' selected' : '' ?>>No</option>
                        <option value="1" <?php echo (isset($_moderated) && $_moderated == 1) ? ' selected' : '' ?>>Yes</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Start New Quilt" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<script>
    (function () {
        var numericFieldIds = [
            'quilt_create_tile_width',
            'quilt_create_tile_height',
            'quilt_create_quilt_width',
            'quilt_create_quilt_height',
            'quilt_create_timelimit'
        ];

        for (var i = 0; i < numericFieldIds.length; i++) {
            var field = document.getElementById(numericFieldIds[i]);
            if (!field) {
                continue;
            }

            field.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        }
    })();
</script>

<?php
include('../include/parts/footer.php');

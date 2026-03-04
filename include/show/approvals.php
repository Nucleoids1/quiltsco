<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'approvals';


$_page = getInt('p', 1);

include('../include/parts/header.php');

$countEntries = (new \Databases\Tiles())->countPendingApprovalsByUser($GLOBALS['auth']['id']);
$countPage = ceil($countEntries / 1);
$pages = '<span class="pages">Page: ' . pages('?s=tiles_completed', $_page, $countPage) . '</span><br />';
?>

<h1 class="header">Approve Tiles<?php echo $countEntries ? '<br />' . $pages : '' ?></h1>

<div class="content">
    <?php
    $tilesRows = (new \Databases\Tiles())->selectPendingApprovalsByUser($GLOBALS['auth']['id'], (($_page - 1) * 1), 1);
    if ($tilesRows) {
        foreach ($tilesRows as $tilesRow) {
            $quiltsRow = (new \Databases\Quilts())->findById($tilesRow['quilt_id']);
            ?>
            <table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation">
                <tr>
                    <td>
                        <?php if ($quiltsRow) { ?>
                            <img src="<?php echo safeUrl('?g=tile_sides&i=' . $tilesRow['quilt_id'] . '&x=' . $tilesRow['matrix_x'] . '&y=' . $tilesRow['matrix_y']) ?>" alt="Tile approval preview" style="width: <?php echo $quiltsRow['tile_width'] + ($quiltsRow['side_pixels'] * 2) ?>px; height: <?php echo $quiltsRow['tile_height'] + ($quiltsRow['side_pixels'] * 2) ?>px;">
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <div style="padding: 5px 0px 0px 0px;"></div>
            <h2 class="header">Reasoning</h2>
            <div style="padding: 5px 0px 0px 0px;"></div>
            <form action="<?php echo safeUrl('?a=approvals&i=' . $tilesRow['tile_id']) ?>" method="post" class="form">
                <?php echo csrfField() ?>
                <table style="width: 100%;" role="presentation">
                    <tr>
                        <td class="form_label_cell">Todo:</td>
                        <td class="form_input_cell"><label><input name="todo" type="radio" value="1">Approve</label> <label><input name="todo" type="radio" value="0">Deny</label></td>
                    </tr>
                    <tr>
                        <td class="form_label_cell"><label for="approval_reason">Reason:</label></td>
                        <td class="form_input_cell"><textarea name="reason" id="approval_reason" class="input_text" style="width: 400px; height: 150px;"></textarea></td>
                    </tr>
                    <tr>
                        <td class="form_label_cell"></td>
                        <td class="form_input_cell"><input type="submit" value="Confirm" class="input_submit"></td>
                    </tr>
                </table>
            </form>
            <?php
        }
    } else {
        echo 'You do not have any tiles to approve.';
    }
    ?>
</div>

<?php
if ($countEntries) {
    echo '<div class="header">' . $pages . '</div>';
}

include('../include/parts/footer.php');

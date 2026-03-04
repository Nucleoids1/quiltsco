<?php
require_once('../include/functions/tile_get_sides.php');
require_once('../include/functions/tile_is_available.php');

$GLOBALS['highlight'] = 'quilts';


$_id = getInt('i');
$_x = getInt('x');
$_y = getInt('y');

$quiltsRow = (new \Databases\Quilts())->findById($_id);
if ($quiltsRow) {
    list($available, $display, $output) = tileIsAvailable($_id, $_x, $_y);
    if ($available) {
        (new \Databases\TilesPending())->checkoutTile(
            $_id,
            $_x,
            $_y,
            $GLOBALS['auth']['id'],
            $quiltsRow['timelimit'],
            tileGetSides($_id, $_x, $_y)
        );
    } else {
        header('Location: ./?s=quilt&i=' . $_id);
        die;
    }
} else {
    header('Location: ./?s=quilts');
    die;
}

include('../include/parts/header.php');
?>

<h1 class="header">Checkout</h1>

<div class="content">
    <table style="border-collapse: collapse; border-spacing: 0;" role="presentation">
        <tr>
            <td style="vertical-align: top;">
                <?php echo '<img src="' . safeUrl('?g=tile_sides&i=' . $_id . '&x=' . $_x . '&y=' . $_y) . '" alt="Checked out tile preview" style="width: ' . ($quiltsRow['tile_width'] + $quiltsRow['side_pixels'] * 2) . 'px; height: ' . ($quiltsRow['tile_height'] + $quiltsRow['side_pixels'] * 2) . 'px;">' . "\r\n"; ?>
            </td>
            <td style="padding-left: 10px; vertical-align: top;">
                You have checked out the tile to the left.<br />
                <div style="padding: 10px 0px 0px 0px;"></div>
                To begin working on it, save the image to your hard drive, open it in your favorite art app, and go to work!<br />
                <div style="padding: 10px 0px 0px 0px;"></div>
                When you are ready to check the tile back in, or if you wish to cancel your checked out tile, you will find a listing of all your checked out tiles in the "Tiles Pending" link from the top bar or on the actualy quilt page itself..<br />
                <div style="padding: 10px 0px 0px 0px;"></div>
                Your tile should be saved as a 24-bit PNG file before checking it in.<br />
                <div style="padding: 10px 0px 0px 0px;"></div>
                <?php echo 'Return To Quilt: ' . makeLink(safeUrl('?s=quilt&i=' . $_id), $quiltsRow['name']) . '<br />' . "\r\n"; ?>
            </td>
        </tr>
    </table>
</div>

<?php
include('../include/parts/footer.php');

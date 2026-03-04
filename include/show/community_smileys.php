<?php
$GLOBALS['highlight'] = 'forum';

include('../include/parts/header.php');

echo boxOutsideTop('Smileys', '', '', '', '', 'padding: 0px 0px 5px 5px;');
?>
<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation">
    <tr>
        <td>
            <?php
            $communitySmileysRows = (new \Databases\CommunitySmileys())->selectAllByName();
            foreach ($communitySmileysRows as $communitySmileysRow) {
                echo boxImageTop('width: 100px; height: 50px;');
                echo '<img src="images/smileys/' . $communitySmileysRow['filename'] . '" alt="' . safeAttr($communitySmileysRow['name']) . ' smiley" />';
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
                echo '<b>' . $communitySmileysRow['name'] . '</b>';
                echo boxImageBottom();
            }
            ?>
        </td>
    </tr>
</table>
<?php
echo boxOutsideBottom();

include('../include/parts/footer.php');

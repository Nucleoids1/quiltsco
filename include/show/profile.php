<?php
require_once('../include/functions/comments.php');
require_once('../include/functions/horoscope.php');
require_once('../include/functions/pages.php');
require_once('../include/functions/tile_is_available.php');

$GLOBALS['highlight'] = 'members';


$_user = get('u');

$membersRow = (new \Databases\Members())->findById(getUserId($_user));
if ($membersRow) {
    $membersExtrasRow = (new \Databases\MembersExtras())->findByUserId(getUserId($_user));
    $membersLastonRow = (new \Databases\MembersLaston())->findByUserId(getUserId($_user));
} else {
    header('Location: ./?s=members');
    die;
}

include('../include/parts/header.php');

if ($GLOBALS['auth']['id'] && $GLOBALS['auth']['id'] != getUserId($_user)) {
?>
    <script>
        function makeFriend() {
            ajaxJson('index.php?j=friend&i=<?php echo safeJs(getUserId($_user)) ?>&w=make');
        }

        function removeFriend() {
            ajaxJson('index.php?j=friend&i=<?php echo safeJs(getUserId($_user)) ?>&w=remove');
        }
    </script>
<?php
}
?>

<script>
    function tilesCompleted(page) {
        ajaxPost('index.php?j=profile_tiles_completed&i=<?php echo safeJs(getUserId($_user)) ?>&p=' + page, 'tilesCompleted');
    }

    function graphixCompleted(page) {
        ajaxPost('index.php?j=profile_graphix_completed&i=<?php echo safeJs(getUserId($_user)) ?>&p=' + page, 'graphixCompleted');
    }
</script>

<h1 class="header"><?php echo makeLink(safeUrl('?s=members&d=' . strtolower(substr($membersRow['username'], 0, 1))), 'Members') ?> - User Information</h1>
<div class="content">
    <table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation">
        <tr>
            <td style="vertical-align: top;">
                <table style="width: 100%;" role="presentation">
                    <?php
                    echo '<tr><td class="form_label_cell">Username:</td><td class="form_input_cell">';
                    echo $membersRow['username'];
                    if ($membersExtrasRow['birthday'] && $membersExtrasRow['birthday'] != '0000-00-00 00:00:00') {
                        $age = date('Y') - substr($membersExtrasRow['birthday'], 0, 4);
                        if (substr($membersExtrasRow['birthday'], 5, 2) >= date('m')) {
                            $age--;
                            if (substr($membersExtrasRow['birthday'], 5, 2) == date('m') && substr($membersExtrasRow['birthday'], 8, 2) <= date('d')) {
                                $age++;
                            }
                        }
                        if ($age < 100) {
                            echo ', ' . $age;
                        }
                    }
                    if ($membersExtrasRow['gender']) {
                        echo $membersExtrasRow['gender'] == 1 ? ', Male' : ', Female';
                    }
                    if ($membersExtrasRow['birthday'] && $membersExtrasRow['birthday'] != '0000-00-00') {
                        if ($sign = getHoroscopeSign($membersExtrasRow['birthday'])) {
                            echo ' (' . $sign . ')';
                        }
                    }
                    if ($GLOBALS['auth']['id']) {
                        echo ' [ ' . makeLink(safeUrl('?s=messages&u=' . $membersRow['username']), 'Send Message') . ' ]';
                        if ($GLOBALS['auth']['id'] != getUserId($_user)) {
                            $friendsRow = (new \Databases\Friends())->findByUserIdAndFriendId($GLOBALS['auth']['id'], getUserId($_user));
                            if ($friendsRow) {
                                echo ' [ <span id="friend">' . makeLink('javascript: removeFriend();', 'Remove Friend') . '</span> ]';
                            } else {
                                echo ' [ <span id="friend">' . makeLink('javascript: makeFriend();', 'Make Friend') . '</span> ]';
                            }
                        }
                    }
                    echo '</td></tr>';
                    if ($membersExtrasRow['country']) {
                        echo '<tr><td class="form_label_cell">Location:</td><td class="form_input_cell">';
                        if ($membersExtrasRow['country']) {
                            echo $membersExtrasRow['country'];
                        }
                        if ($membersExtrasRow['region']) {
                            echo ', ' . $membersExtrasRow['region'];
                        }
                        if ($membersExtrasRow['city']) {
                            echo ', ' . $membersExtrasRow['city'];
                        }
                        echo '</td></tr>';
                    }
                    if ($membersLastonRow && $membersLastonRow['laston'] != '0000-00-00 00:00:00') {
                        echo '<tr><td class="form_label_cell">Laston:</td><td class="form_input_cell">' . niceDate($membersLastonRow['laston']) . '</td></tr>';
                    }
                    if ($membersExtrasRow['fullname']) {
                        echo '<tr><td class="form_label_cell">Full Name:</td><td class="form_input_cell">' . $membersExtrasRow['fullname'] . '</td></tr>';
                    }
                    if ($membersExtrasRow['website']) {
                        echo '<tr><td class="form_label_cell">Website:</td><td class="form_input_cell">';
                        $website = trim($membersExtrasRow['website']);
                        if (strpos(strtolower($website), 'http://') === 0) {
                            $website = 'https://' . substr($website, 7);
                        }
                        if (!preg_match('/^https?:\/\//i', $website)) {
                            $website = 'https://' . $website;
                        }
                        echo makeLink(safeUrl($website), safeAttr($website));
                        echo '</td></tr>';
                    }
                    if ($membersExtrasRow['privacy'] == 0) {
                        if ($membersExtrasRow['aim']) {
                            echo '<tr><td class="form_label_cell">AOL Messenger:</td><td class="form_input_cell">' . $membersExtrasRow['aim'] . '</td></tr>';
                        }
                        if ($membersExtrasRow['icq']) {
                            echo '<tr><td class="form_label_cell">ICQ Messenger:</td><td class="form_input_cell">' . $membersExtrasRow['icq'] . '</td></tr>';
                        }
                        if ($membersExtrasRow['msn']) {
                            echo '<tr><td class="form_label_cell">MSN Messenger:</td><td class="form_input_cell">' . $membersExtrasRow['msn'] . '</td></tr>';
                        }
                        if ($membersExtrasRow['yahoo']) {
                            echo '<tr><td class="form_label_cell">Yahoo Messenger:</td><td class="form_input_cell">' . $membersExtrasRow['yahoo'] . '</td></tr>';
                        }
                        if ($membersExtrasRow['gtalk']) {
                            echo '<tr><td class="form_label_cell">Google Talk:</td><td class="form_input_cell">' . $membersExtrasRow['gtalk'] . '</td></tr>';
                        }
                    }
                    $tilesCount = (new \Databases\Tiles())->countVisibleByUser(getUserId($_user));
                    $tilesSeconds = (new \Databases\Tiles())->sumVisibleSecondsByUser(getUserId($_user));
                    if ($tilesCount) {
                        echo '<tr><td class="form_label_cell">Average Checkin Time:</td><td class="form_input_cell">' . round($tilesSeconds / $tilesCount / 60, 2) . ' minutes</td></tr>';
                    }
                    ?>
                </table>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <?php
                echo '<img src="?g=thumb&i=' . getMainImageId(getUserId($_user)) . '" alt="Profile photo of ' . safeAttr($_user) . '" style="width: ' . THUMB_WIDTH . 'px; height: ' . THUMB_HEIGHT . 'px; border: solid 2px black;">';
                ?>
            </td>
        </tr>
    </table>
    <?php
    echo '</div>';

    $_id = getUserId($_user);
    $_page = 1;
    echo '<div id="tilesCompleted">';
    include('../include/ajax/profile_tiles_completed.php');
    echo '</div>';

    echo '<div id="graphixCompleted">';
    include('../include/ajax/profile_graphix_completed.php');
    ?>
</div>
</div>
<?php
comments(getUserId($_user), 'members_comments');

include('../include/parts/footer.php');

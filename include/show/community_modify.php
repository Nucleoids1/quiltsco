<?php
require_once('../include/functions/community_permissions.php');
require_once('../include/functions/ip_decode.php');

$GLOBALS['highlight'] = 'forum';

//SAFE SHOW


$_id = getInt('i');

$communityRow = (new \Databases\Community())->findById($_id);
if (!$communityRow) {
    makeCookie('notice', 'Sorry, but this community does not exist.');
    header('Location: ./?s=community_create');
    die;
}

communityPermissions($communityRow['community_id']);

if (!$GLOBALS['auth']['community']['administration'] && !$GLOBALS['auth']['community']['administration_ban_ip'] && !$GLOBALS['auth']['community']['administration_ban_user']) {
    makeCookie('notice', 'Sorry, you don\'t have permission to modify this community.');
    header('Location: ./?s=community_create');
    die;
}

include('../include/parts/header.php');

if ($GLOBALS['auth']['community']['administration']) {
    echo boxOutsideTop('Modify Your Community');
    echo boxInsideTop();
?>

    <form action="<?php echo safeUrl('?a=community_modify&i=' . $communityRow['community_id']) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <label for="community_modify_name"><b>Community Name</b></label> (<?php echo COMMUNITY_NAME_MIN ?> to <?php echo COMMUNITY_NAME_MAX ?> characters in length. No spaces.)
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="text" name="name" id="community_modify_name" maxlength="<?php echo COMMUNITY_NAME_MAX ?>" class="input_text" value="<?php echo safeAttr($communityRow['community_name']); ?>" style="width: 400px;">
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Modfify Community" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if ($GLOBALS['auth']['community']['administration']) {
    echo boxOutsideTop('Community Moderators');
    $i = 0;
    $communityPermissionsRows = (new \Databases\CommunityPermissions())->selectDistinctModeratorsByCommunity($communityRow['community_id']);
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo boxInsideTop();
        displayAdminPerms($communityPermissionsRow['community_id'], $communityPermissionsRow['user_id'], true);
        echo boxInsideBottom();
    }
    $communityPermissionsRows = (new \Databases\CommunityPermissions())->selectDistinctNonModeratorAdminsByCommunity($communityRow['community_id']);
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        if ($i++) {
            echo '<div style="padding: 5px 0px 0px 0px;"></div>';
        }
        echo boxInsideTop();
        displayAdminPerms($communityPermissionsRow['community_id'], $communityPermissionsRow['user_id'], false);
        echo boxInsideBottom();
    }
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo boxInsideTop();
?>

    <form action="<?php echo safeUrl('?a=community_admin_add&i=' . $communityRow['community_id']) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <label for="community_modify_admin_name"><b>Add A Moderator</b></label> (enter username)
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="text" name="admin_name" id="community_modify_admin_name" class="input_text" style="width: 400px;">
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Add Moderator" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if ($GLOBALS['auth']['community']['administration']) {
    echo boxOutsideTop('Message Board');
    echo boxInsideTop();

    $i = 0;
    $communitySectionsRows = (new \Databases\CommunitySections())->selectActiveByCommunityOrdered($communityRow['community_id']);
    $sectionCount = count($communitySectionsRows);
    if ($sectionCount) {
        foreach ($communitySectionsRows as $communitySectionsRow) {
            $forumCount = (new \Databases\CommunityForums())->countActiveBySection($communitySectionsRow['section_id']);
            if ($i > 0) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            echo '<div class="' . ($i % 2 ? 'on' : 'off') . '">';
            echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr>';
            echo '<td><span style="font-size: 1.05rem; font-weight: bold;">';
            echo makeLink('?s=community_section_modify&i=' . $communitySectionsRow['section_id'], safeAttr($communitySectionsRow['section_name_english']));
            echo '</span> [' . makePostLink('?a=community_section_delete&i=' . $communitySectionsRow['section_id'], 'remove', 'Are you sure you want to delete this section?') . ']';
            echo '<br /> Contains ' . $forumCount . ' forums';
            echo '</td>';
            if ($i > 0) {
                echo '<td style="width: 75px;">' . makePostLink('?a=community_section_up&i=' . $communitySectionsRow['section_id'], 'Move Up') . '</td>';
            } else {
                echo '<td style="width: 75px; white-space: nowrap;"></td>';
            }
            if ($i < ($sectionCount - 1)) {
                echo '<td style="width: 75px;">' . makePostLink('?a=community_section_down&i=' . $communitySectionsRow['section_id'], 'Move Down') . '</td>';
            } else {
                echo '<td style="width: 75px; white-space: nowrap;"></td>';
            }
            echo '</tr></table>';
            echo '</div>';
            $i++;
        }
    } else {
        echo 'You do not have any forum sections created.';
    }

    echo boxInsideBottom();
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo boxInsideTop();
?>

    <form action="<?php echo safeUrl('?a=community_section_add&i=' . $communityRow['community_id']) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <div class="patch_fill">
            <label for="community_modify_section_name"><b>Add A Section</b></label> (<?php echo COMMUNITY_SECTION_NAME_MIN ?> to <?php echo COMMUNITY_SECTION_NAME_MAX ?> characters in length.)
            <div style="padding: 5px 0px 0px 0px;"></div>
            <input type="text" name="name" id="community_modify_section_name" maxlength="<?php echo COMMUNITY_SECTION_NAME_MAX; ?>" class="input_text" style="width: 400px;">
        </div>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Add Section" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if ($GLOBALS['auth']['community']['administration_ban_user']) {
    echo boxOutsideTop('Banned Users');
    echo boxInsideTop();

    $i = 0;
    $bannedCommunityUsersArr = array();
    $communityBannedUsersRows = (new \Databases\CommunityBannedUsers())->selectByCommunityOrdered($communityRow['community_id']);
    if (count($communityBannedUsersRows)) {
        foreach ($communityBannedUsersRows as $communityBannedUsersRow) {
            if ($i++) {
                echo '<div style="padding: 5px 0px 0px 0px;"></div>';
            }
            echo '<div class="' . ($i % 2 == 1 ? 'on' : 'off') . '">';
            echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="width: 200px; vertical-align: top;">';
            echo makeLink(safeUrl('?s=profile&u=' . getUsername($communityBannedUsersRow['user_id'])), '<b>' . safeAttr(getUsername($communityBannedUsersRow['user_id'])) . '</b>');
            echo '</td><td style="vertical-align: top;">';
            $j = 0;
            $statsIpsRows = (new \Databases\StatsIps())->selectByUserOrderedIp($communityBannedUsersRow['user_id']);
            foreach ($statsIpsRows as $statsIpsRow) {
                if ($j++) {
                    echo ', ';
                }
                echo decodeIp($statsIpsRow['ip']);
            }
            echo '</td><td style="width: 80px; text-align: right; vertical-align: top;" class="notice_attention">';
            echo makePostLink('?a=community_user_unban&i=' . $communityRow['community_id'] . '&name=' . getUsername($communityBannedUsersRow['user_id']), 'Unban');
            echo '</td></tr></table>';
            echo '</div>';
        }
    } else {
        echo 'There are no banned users.';
    }

    echo boxInsideBottom();
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo boxInsideTop();
?>

    <form action="<?php echo safeUrl('?a=community_user_ban&i=' . $communityRow['community_id']) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <div class="patch_fill">
            <label for="community_modify_ban_user"><b>Ban A User</b></label>
            <div style="padding: 5px 0px 0px 0px;"></div>
            <input type="text" name="name" id="community_modify_ban_user" class="input_text" style="width: 300px;" />
        </div>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Ban User" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

if ($GLOBALS['auth']['community']['administration_ban_ip']) {
    echo boxOutsideTop('Banned IPs');
    echo boxInsideTop();

    $i = 0;
    $communityBannedIpsRows = (new \Databases\CommunityBannedIps())->selectByCommunityOrdered($communityRow['community_id']);
    if (count($communityBannedIpsRows)) {
        foreach ($communityBannedIpsRows as $communityBannedIpsRow) {
            echo '<div class="' . ($i % 2 ? 'on' : 'off') . '">';
            echo '<table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation"><tr><td style="width: 200px; vertical-align: top;">';
            echo decodeIp($communityBannedIpsRow['ip']);
            echo '</td><td style="vertical-align: top;">';
            $j = 0;
            $statsIpsRows = (new \Databases\StatsIps())->selectByIpWithKnownUsers($communityBannedIpsRow['ip']);
            foreach ($statsIpsRows as $statsIpsRow) {
                if ($j++) {
                    echo ', ';
                }
                echo getUsername($statsIpsRow['user_id']);
            }
            echo '</td><td style="width: 80px; text-align: right; vertical-align: top;" class="notice_attention">';
            echo makePostLink('?a=community_ip_unban&i=' . $communityRow['community_id'] . '&ip=' . decodeIp($communityBannedIpsRow['ip']), 'Unban');
            echo '</td></tr></table>';
            echo '</div>';
        }
    } else {
        echo 'There are no banned ips.';
    }

    echo boxInsideBottom();
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo boxInsideTop();
?>

    <form action="<?php echo safeUrl('?a=community_ip_ban&i=' . $communityRow['community_id']) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <div class="patch_fill">
            <label for="community_modify_ban_ip"><b>Ban An IP Address</b></label>
            <div style="padding: 5px 0px 0px 0px;"></div>
            <input type="text" name="ip" id="community_modify_ban_ip" class="input_text" style="width: 300px;" />
        </div>
        <div style="padding: 5px 0px 0px 0px;"></div>
        <input type="submit" value="Ban IP" class="input_submit">
    </form>

<?php
    echo boxInsideBottom();
    echo boxOutsideBottom();
}

include('../include/parts/footer.php');

function displayAdminPerms($communityId, $userId, $root)
{
    $username = getUsername($userId);
    $perms = '';
    $permArr = array();
    $i = 0;
    if ($root) {
        $permissionsRows = (new \Databases\CommunityPermissions())->selectDistinctPermissions();
    } else {
        $permissionsRows = (new \Databases\CommunityPermissions())->selectPermissionsByCommunityAndUser($communityId, $userId);
    }
    foreach ($permissionsRows as $permissionsRow) {
        $permArr[$i++] = $permissionsRow['permission'];
    }
    $perms = implode(', ', $permArr);
    echo '[' . $userId . '] <span style="font-size: 1.05rem; font-weight: bold;">';
    if ($root) {
        echo safeAttr($username);
    } else {
        echo makeLink(safeUrl('?s=community_admin_modify&i=' . $communityId . '&admin_name=' . $username), safeAttr($username));
    }
    echo '</span> [' . makeLink(safeUrl('?s=profile&u=' . $username), 'info') . ']';
    if ($root) {
        echo ' [root admin]';
    } else {
        echo ' [' . makePostLink('?a=community_admin_delete&i=' . $communityId . '&admin_name=' . $username, 'remove', 'Are you sure you want to remove this admin?') . ']';
    }
    echo '<div style="padding: 5px 0px 0px 0px;"></div>';
    echo $perms;
}

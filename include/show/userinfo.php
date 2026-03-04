<?php
require_once('../include/functions/pages.php');

$GLOBALS['highlight'] = 'userinfo';


$membersRow = (new \Databases\Members())->findById($GLOBALS['auth']['id']);
if (!$membersRow) {
    header('Location: ./?s=finished');
    die;
}
$membersExtrasRow = (new \Databases\MembersExtras())->findByUserId($GLOBALS['auth']['id']);

include('../include/parts/header.php');
?>

<h1 class="header">User Information</h1>
<?php
if ($_notice) {
    echo '<div class="content">';
    echo '<span style="color: red;">None of your changes have been changed because...</span><br />';
    echo '<span style="color: red;">' . $_notice . '</span>';
    echo '</div>';
}
?>
<div class="content">
    <form action="?a=userinfo" method="post" style="margin: 0px;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="userinfo_fullname">Full Name:</label></td>
                <td><input name="fullname" id="userinfo_fullname" value="<?php echo safeAttr($membersExtrasRow['fullname']) ?>" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_month">Birthday:</label></td>
                <td>
                    <label for="userinfo_month" class="hidden">Month</label>
                    <select class="input_select" name="month" id="userinfo_month">
                        <option value="0">Month:</option>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $selected = (substr($membersExtrasRow['birthday'], 5, 2) == $i) ? ' selected' : '';
                            echo '<option value="' . $i . '"' . $selected . '>' . date("F", mktime(0, 0, 0, $i, 1, 2000)) . '</option>';
                        }
                        ?>
                    </select>
                    <label for="userinfo_day" class="hidden">Day</label>
                    <select class="input_select" name="day" id="userinfo_day">
                        <option value="0">Day:</option>
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            $selected = (substr($membersExtrasRow['birthday'], 8, 2) == $i) ? ' selected' : '';
                            echo '<option value="' . $i . '"' . $selected . '>' . date("d", mktime(0, 0, 0, 1, $i, 2000)) . '</option>';
                        }
                        ?>
                    </select>
                    <label for="userinfo_year" class="hidden">Year</label>
                    <select class="input_select" name="year" id="userinfo_year">
                        <option value="0">Year:</option>
                        <?php
                        for ($i = date('Y') - 12; $i >= 1920; $i--) {
                            $selected = (substr($membersExtrasRow['birthday'], 0, 4) == $i) ? ' selected' : '';
                            echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_gender">Gender:</label></td>
                <td><select class="input_select" name="gender" id="userinfo_gender">
                        <option value="0" <?php echo ($membersExtrasRow['gender'] == 0) ? ' selected' : '' ?>>Not Disclosed</option>
                        <option value="1" <?php echo ($membersExtrasRow['gender'] == 1) ? ' selected' : '' ?>>Male</option>
                        <option value="2" <?php echo ($membersExtrasRow['gender'] == 2) ? ' selected' : '' ?>>Female</option>
                    </select></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="country">Country:</label></td>
                <td id="country_area"><select class="input_select" name="country" id="country" onChange="updateRegion(document.getElementById('country').value);">
                        <option value=""></option>
                        <?php
                        $countryId = 0;
                        $geoCountriesRows = (new \Databases\GeoCountries())->findAllOrderedByCountryId();
                        foreach ($geoCountriesRows as $geoCountriesRow) {
                            $selected = ($geoCountriesRow['country_name'] == $membersExtrasRow['country']) ? ' selected' : '';
                            if ($selected) {
                                $countryId = $geoCountriesRow['country_id'];
                            }
                            echo '<option value="' . $geoCountriesRow['country_id'] . '"' . $selected . '>' . $geoCountriesRow['country_name'] . '</option>';
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="region">Region:</label></td>
                <td id="region_area"><select class="input_select" name="region" id="region" onChange="updateCity(document.getElementById('region').value);">
                        <?php
                        if ($countryId) {
                            $geoRegionsRows = (new \Databases\GeoRegions())->findByCountryId($countryId);
                            if ($geoRegionsRows) {
                                foreach ($geoRegionsRows as $geoRegionsRow) {
                                    $selected = ($membersExtrasRow['region'] == $geoRegionsRow['region_name']) ? ' selected' : '';
                                    if ($selected) {
                                        $regionId = $geoRegionsRow['region_id'];
                                    }
                                    echo '<option value="' . $geoRegionsRow['region_id'] . '"' . $selected . '>' . $geoRegionsRow['region_name'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No Regions Available</option>';
                            }
                        } else {
                            echo '<option value="">Choose a Country</option>';
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="city">City:</label></td>
                <td id="city_area"><select class="input_select" name="city" id="city">
                        <?php
                        if ($regionId) {
                            $geoCitiesRows = (new \Databases\GeoCities())->findByRegionId($regionId);
                            if ($geoCitiesRows) {
                                foreach ($geoCitiesRows as $geoCitiesRow) {
                                    $selected = ($membersExtrasRow['city'] == $geoCitiesRow['city_name']) ? ' selected' : '';
                                    echo '<option value="' . $geoCitiesRow['city_id'] . '"' . $selected . '>' . $geoCitiesRow['city_name'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No Cities Available</option>';
                            }
                        } else {
                            echo '<option value="">Choose a Region</option>';
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_website">Website:</label></td>
                <td><input name="website" id="userinfo_website" value="<?php echo safeAttr($membersExtrasRow['website']) ?>" class="input_text" style="width: 240px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_aim">AOL Messenger:</label></td>
                <td><input name="aim" id="userinfo_aim" value="<?php echo safeAttr($membersExtrasRow['aim']) ?>" class="input_text" style="width: 160px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_icq">ICQ Messenger:</label></td>
                <td><input name="icq" id="userinfo_icq" value="<?php echo ($membersExtrasRow['icq'] != 0) ? safeAttr($membersExtrasRow['icq']) : '' ?>" class="input_text" style="width: 160px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_msn">MSN Messenger:</label></td>
                <td><input name="msn" id="userinfo_msn" value="<?php echo safeAttr($membersExtrasRow['msn']) ?>" class="input_text" style="width: 240px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_yahoo">Yahoo Messenger:</label></td>
                <td><input name="yahoo" id="userinfo_yahoo" value="<?php echo safeAttr($membersExtrasRow['yahoo']) ?>" class="input_text" style="width: 240px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="userinfo_gtalk">Google Talk:</label></td>
                <td><input name="gtalk" id="userinfo_gtalk" value="<?php echo safeAttr($membersExtrasRow['gtalk']) ?>" class="input_text" style="width: 240px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell">Options:</td>
                <td>
                    <label for="userinfo_privacy"><input name="privacy" id="userinfo_privacy" type="checkbox" value="1" <?php echo $membersExtrasRow['privacy'] ? ' checked' : '' ?>> Show Messenger Info Only To My Friends</label><br />
                    <label for="userinfo_notification"><input name="notification" id="userinfo_notification" type="checkbox" value="1" <?php echo $membersExtrasRow['notification'] ? ' checked' : '' ?>> Email Notification On New Message</label><br />
                </td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Save User Info" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<script>
    if (true) {
        init_province();
        initCity();
    }

    function initRegion() {
        document.getElementById('region_area').innerHTML = '<select class="input_select" name="region" id="region" onChange="updateCity(document.getElementById(\'region\').value);"><option value="">Choose a Country</option></select>';
    }

    function initCity() {
        document.getElementById('city_area').innerHTML = '<select class="input_select" name="city" id="city"><option value="">Choose a Region</option></select>';
    }

    function updateRegion(id) {
        initRegion();
        initCity();
        ajaxPost('index.php?j=regions&p=1&i=' + id, 'region_area');
    }

    function updateCity(id) {
        initCity();
        ajaxPost('index.php?j=regions&p=2&i=' + id, 'city_area');
    }
</script>

<?php
$communityPermissionsRows = (new \Databases\CommunityPermissions())->selectAdministratorsByUser($GLOBALS['auth']['id']);
if ($communityPermissionsRows) {
    echo boxOutsideTop('Active Communities');
    echo boxInsideTop();
    foreach ($communityPermissionsRows as $communityPermissionsRow) {
        $communityRow = (new \Databases\Community())->findById($communityPermissionsRow['community_id']);
        if ($communityRow && !$communityRow['community_deleted']) {
            echo makeLink('?s=community_modify&i=' . $communityPermissionsRow['community_id'], htmlentities($communityRow['community_name'])) . '<br />';
        }
    }
    echo boxInsideBottom();
    echo boxOutsideBottom();
}
?>

<h2 class="header">Account Settings (Validation Required)</h2>

<div class="content">
    <form action="?a=account" method="post" style="margin: 0px;">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr style="background: #444444;">
                <td class="form_label_cell"><label for="account_old">Old Password:</label></td>
                <td><input name="old" id="account_old" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="account_username">Username:</label></td>
                <td><input name="username" id="account_username" value="<?php echo safeAttr($membersRow['username']) ?>" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="account_pass1">New Password:</label></td>
                <td><input name="pass1" id="account_pass1" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="account_pass2">Retype Password:</label></td>
                <td><input name="pass2" id="account_pass2" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="account_email">Email:</label></td>
                <td><input name="email" id="account_email" value="<?php echo safeAttr($membersRow['email']) ?>" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Save Account Settings" class="input_submit"></td>
            </tr>
        </table>
    </form>
    <br />* Leave your password blank to keep it as it was. If your email address is being changed you will have to validate it before the changes will take effect.<br />
    <?php
    $membersCreateRow = (new \Databases\MembersCreate())->findByUserId($GLOBALS['auth']['id']);
    if ($membersCreateRow) {
        echo '<div style="padding: 10px 0px 0px 0px;"></div>';
        echo '<span style="color: #c7ff70; font-weight: bold;">Email Validation</span><br />';
        echo '<div style="padding: 10px 0px 0px 0px;"></div>';
        echo '<form action="?a=email" method="post" style="margin: 0px;">';
        echo csrfField();
        echo 'In order to change your email to ' . $membersCreateRow['email'] . ' you must submit the validation code which was emailed to you. This will be cancelled in 12 hours if you do not receive it and you can try again.<br />';
        echo '<div style="padding: 10px 0px 0px 0px;"></div>';
        echo '<table style="width: 100%;" role="presentation">';
        echo '<tr><td class="form_label_cell"><label for="email_validation_cache">Validation Key:</label></td><td class="form_input_cell"><input name="cache" id="email_validation_cache" class="input_text" style="width: 320px;"></td></tr>';
        echo '<tr><td class="form_label_cell"></td><td class="form_input_cell"><input type="submit" value="Update Email Address" class="input_submit"></td></tr>';
        echo '</table>';
        echo '</form>';
    }
    ?>
    </td>
    </tr>
    </table>
</div>

<?php
include('../include/parts/footer.php');

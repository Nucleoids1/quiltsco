<?php
$GLOBALS['highlight'] = 'home';

$_cache = get('cache');

$tokenTtlSeconds = defined('MEMBERS_CREATE_TOKEN_TTL_SECONDS') ? MEMBERS_CREATE_TOKEN_TTL_SECONDS : 60 * 60 * 24;
$membersCreateSince = date('Y-m-d H:i:s', server('REQUEST_TIME') - $tokenTtlSeconds);

$membersCreateRow = (new \Databases\MembersCreate())->findValidByCache($_cache, $membersCreateSince);
if (!$membersCreateRow) {
    makeCookie('notice', 'This link has expired. Please request a new password reset email to continue.');
    header('Location: ./?s=password');
    die;
}

include('../include/parts/header.php');
?>

<h1 class="header">Set New Password</h1>

<div class="content">
    <form action="<?php echo safeUrl('?a=password_complete&cache=' . $_cache) ?>" method="post" class="form">
        <?php echo csrfField(); ?>
        <table style="width: 100%;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="password_complete_pass1">Password:</label></td>
                <td><input type="password" name="pass1" id="password_complete_pass1" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="password_complete_pass2">Retype Password:</label></td>
                <td><input type="password" name="pass2" id="password_complete_pass2" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td><input type="submit" value="Set New Password" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<?php
include('../include/parts/footer.php');

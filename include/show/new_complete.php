<?php
$GLOBALS['highlight'] = 'home';

if ($GLOBALS['auth']['id']) {
    header('Location: ./');
    die;
}

$_cache = get('c');

$tokenTtlSeconds = defined('MEMBERS_CREATE_TOKEN_TTL_SECONDS') ? MEMBERS_CREATE_TOKEN_TTL_SECONDS : 60 * 60 * 24;
$membersCreateSince = date('Y-m-d H:i:s', server('REQUEST_TIME') - $tokenTtlSeconds);

$membersCreateRow = (new \Databases\MembersCreate())->findValidByCache($_cache, $membersCreateSince);
if (!$membersCreateRow) {
    makeCookie('notice', 'This link has expired. Please request a new email to continue.');
    header('Location: ./?s=new');
    die;
}

include('../include/parts/header.php');
?>

<h1 class="header">Create An Account</h1>
<?php
if ($_notice) {
    echo '<div class="content">';
    echo '<span class="notice_error">' . $_notice . '</span>';
    echo '</div>';
}
?>
<div class="content">
    <form action="?a=new_complete" method="post" style="margin: 0px; display: inline;">
        <?php echo csrfField(); ?>
        <input type="hidden" name="cache" value="<?php echo safeAttr($_cache); ?>">
        <table style="width: 100%;" role="presentation">
            <?php
            echo '<tr><td class="form_label_cell"><label for="new_complete_name">Username:</label></td><td class="form_input_cell"><input name="name" id="new_complete_name" class="input_text" style="width: 320px;"></td></tr>';
            echo '<tr><td class="form_label_cell"><label for="new_complete_pass1">Password:</label></td><td class="form_input_cell"><input name="pass1" id="new_complete_pass1" class="input_text" style="width: 320px;"></td></tr>';
            echo '<tr><td class="form_label_cell"><label for="new_complete_pass2">Retype Password:</label></td><td class="form_input_cell"><input name="pass2" id="new_complete_pass2" class="input_text" style="width: 320px;"></td></tr>';
            echo '<tr><td class="form_label_cell"></td><td class="form_input_cell"><input type="submit" value="Create Account" class="input_submit"></td></tr>';
            ?>
        </table>
    </form>
</div>

<?php
include('../include/parts/footer.php');

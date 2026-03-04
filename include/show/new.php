<?php
$GLOBALS['highlight'] = 'home';

if (!cookie('cache')) {
    makeCookie('cache', makeCacheCode());
}

include('../include/parts/header.php');
?>

<h1 class="header">Create New Account</h1>
<?php
if ($_notice) {
    echo '<div class="content">';
    echo '<span class="notice_error">' . $_notice . '</span>';
    echo '</div>';
}
?>
<div class="content">
    Please enter your email address, you will receive a confirmation email which will allow you to continue the registration process.<br /><br />
    <form action="?a=new" method="post" style="margin: 0px;">
        <?php echo csrfField(); ?>
        <table style="width: 100%; border-collapse: collapse; border-spacing: 0;" role="presentation">
            <tr>
                <td class="form_label_cell"><label for="new_email">Email Address:</label></td>
                <td class="form_input_cell"><input name="email" id="new_email" class="input_text" style="width: 320px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td class="form_input_cell"><img src="?s=security_code&rand=<?php echo random_int(intval('1' . str_repeat('0', strlen(strval(PHP_INT_MAX)) - 1)), PHP_INT_MAX); ?>" alt="Security code - enter the characters shown" style="border: 0; vertical-align: middle;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"><label for="password_security_code">Security Code:</label></td>
                <td class="form_input_cell"><input type="text" name="security_code" id="password_security_code" class="input_text" style="width: 100px;"></td>
            </tr>
            <tr>
                <td class="form_label_cell"></td>
                <td class="form_input_cell"><input type="submit" value="Get Confirmation Email" class="input_submit"></td>
            </tr>
        </table>
    </form>
</div>

<?php
include('../include/parts/footer.php');

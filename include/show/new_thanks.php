<?php
if ($GLOBALS['auth']['id']) {
    header('Location: ./?s=finished');
    die;
}

$GLOBALS['highlight'] = 'home';

include('../include/parts/header.php');
?>

<h1 class="header">Thank You</h1>
<div class="content">
    If the email is valid, we sent instructions to continue registration.
</div>

<?php
include('../include/parts/footer.php');

<?php
$expired = false;
$arguments = ['expired' => FILTER_SANITIZE_SPECIAL_CHARS];
$updateData = filter_input_array(INPUT_GET, $arguments);
if (isset($updateData['expired'])) {
    $expired = true;
}
?>
<header class="w3-cell-row event-color-primary w3-mobile">
    <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="w3-container w3-cell w3-mobile">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div>
</header>

<div class="w3-container w3-mobile">
  <div class="w3-container w3-center w3-border w3-margin w3-padding">
<?php
if ($expired) {
    echo "<h2 class='event-color-primary'> Expired Password </h2>\n";
} else {
    echo "<h2 class='event-color-primary'> Password Recovery </h2>\n";
}
?>
    <hr>
<?php
if ($expired) {
    $body = <<<BOD
        <span class="w3-center w3-bold">Your password has expired and needs to be reset, enter the login name or e-mail address associated with your account and we'll e-mail infomation to reset your password.</span>
BOD;
} else {
    $body = <<<BOD
        <span class="w3-center w3-bold">If you forgot your password, enter the login name or e-mail address associated with your account and we'll e-mail infomation to reset your password.</span>
BOD;
}
echo $body."\n";
?>
    <div>
        <input class="w3-center w3-input w3-border" id="email"></input>
        <button class="w3-button event-color-primary w3-margin" onclick="resetPassword()">Reset Password</button>
    </div>
  </div>
</div>

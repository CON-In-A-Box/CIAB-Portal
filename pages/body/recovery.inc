<?php
/*.
    require_module 'standard';
.*/

$expired = false;
$reset = false;
$arguments = ['expired' => FILTER_SANITIZE_SPECIAL_CHARS,
'email' => FILTER_SANITIZE_SPECIAL_CHARS,
'authorization' => FILTER_SANITIZE_SPECIAL_CHARS];
$updateData = filter_input_array(INPUT_GET, $arguments);
if (isset($updateData['expired'])) {
    $expired = true;
}
if (isset($updateData['email']) && isset($updateData['authorization'])) {
    $reset = true;
}
?>
<header class="UI-page-title-bar">
    <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="UI-page-title">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div>
</header>

<div class="UI-maincontent">
  <div class="UI-container UI-center UI-border UI-margin UI-padding">
<?php
if ($expired) {
    echo "<h2 class='event-color-primary'> Expired Password </h2>\n";
} else {
    echo "<h2 class='event-color-primary'> Password Recovery </h2>\n";
}
?>
    <hr>
<?php
if ($reset) {
?>
    <div class='UI-container UI-border'>
        <label class="UI-label" for="email">Account Login</label>
        <input class="UI-center UI-input" id="email" readonly value="<?php echo $updateData['email'];?>"></input>
        <br>
        <label class="UI-label UI-margin" for="new_password">New Password</label>
        <input type="password" class="UI-center UI-input" id="new_password" value="">
        <br>
        <label class="UI-label UI-margin" for="confirm_password">Confirm Password</label>
        <input type="password" class="UI-center UI-input" id="confirm_password" value="">
        <input class="UI-hide" id="authorization" readonly value="<?php echo $updateData['authorization'];?>">
        <br>
        <button class="UI-eventbutton UI-margin" onclick="newPassword()">Reset Password</button>
    </div>
<?php
} else {
    if ($expired) {
        $body = <<<BOD
        <span class="UI-recovery-text-block">Your password has expired and needs to be reset, enter the login name or e-mail address associated with your account and we'll e-mail infomation to reset your password.</span>
BOD;
    } else {
        $body = <<<BOD
        <span class="UI-recovery-text-block">If you forgot your password, enter the login name or e-mail address associated with your account and we'll e-mail infomation to reset your password.</span>
BOD;
    }
    echo $body."\n";
?>
    <div>
        <input class="UI-center UI-input" id="email"></input>
        <button class="UI-eventbutton UI-margin" onclick="resetPassword()">Reset Password</button>
        <button class="UI-redbutton UI-margin" onclick="window.history.back();">Back</button>
    </div>
<?php } ?>
  </div>
</div>

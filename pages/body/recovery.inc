<?php
$expired = false;
$arguments = ['expired' => FILTER_SANITIZE_SPECIAL_CHARS];
$updateData = filter_input_array(INPUT_GET, $arguments);
if (isset($updateData['expired'])) {
    $expired = true;
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
    </div>
  </div>
</div>

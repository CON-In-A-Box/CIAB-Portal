<?php

$active = (isset($_SESSION['accountId']));

// Dirty Hack - if country isn't set, default to the first country in the list
if (empty($_SESSION['accountInfo']['AddressCountry'])) {
    $_SESSION['accountInfo']['AddressCountry'] = 'United States of America';
}

// Dirty Hack - if state isn't set and provice is empty, default to Minnesota, because I can
if (empty($_SESSION['state']) && empty($_SESSION['accountInfo']['province'])) {
    $_SESSION['state'] = 'MN';
}
?>
<?php if ($active) { ?>
<h3>Your current profile information</h3>
<form action="index.php?Function=profile" method="post" onsubmit="return validateForm(this)" name="Profile" id="profile_update" class="UI-container">
<?php } else { ?>
<h3>New Account Information</h3>
<form name="Profile" id="profile_update" class="UI-container">
<?php } ?>

  <input type="hidden" name="accountId" value="<?php echo $_SESSION['accountId']; ?>">
  <div class="UI-profile-name-div">
    <div>
      Legal Name
<?php if ($active) {?>
      <button class="UI-profile-update-button">Update Profile</button>
<?php }?>
    </div>
    <div class="UI-container">
      <div class="UI-half">
        <div class="UI-twothird">
          <input type="text" name="firstName" value="<?php echo $_SESSION['firstName']; ?>" class="UI-input" placeholder="First (Required)">
        </div>

        <div class="UI-third">
          <input type="text" name="middleName" value="<?php echo $_SESSION['accountInfo']['MiddleName']; ?>" class="UI-input" placeholder="Middle">
        </div>
      </div>

      <div class="UI-half">
        <div class="UI-threequarter">
          <input type="text" name="lastName" value="<?php echo $_SESSION['lastName']; ?>" class="UI-input" placeholder="Last (Required)">
        </div>

        <div class="UI-quarter">
          <input type="text" name="suffix" value="<?php echo $_SESSION['accountInfo']['Suffix']; ?>" class="UI-input" placeholder="Suffix">
        </div>
      </div>
    </div>
  </div>

  <div class="UI-profile-pref-name-div">
    <div>
      Preferred Name
<?php if ($active) {?>
      <button class="UI-profile-update-button">Update Profile</button>
<?php }?>
    </div>
    <div class="UI-container">
      <div class="UI-half">
        <input type="text" name="preferredFirstName" value="<?php
        if (isset($_SESSION['accountInfo']['PreferredFirstName'])) {
            echo $_SESSION['accountInfo']['PreferredFirstName'];
        }
?>" class="UI-input" placeholder="First - If Different">
      </div>

      <div class="UI-half">
        <input type="text" name="preferredLastName" value="<?php
        if (isset($_SESSION['accountInfo']['PreferredLastName'])) {
            echo $_SESSION['accountInfo']['PreferredLastName'];
        }
?>" class="UI-input" placeholder="Last - If Different">
      </div>
    </div>
  </div>

<?php if ($active) {?>
  <div class="UI-profile-badge-number-div">
    <div>
      Badge Number
    </div>
    <div class="UI-container">
        <input type="text" name="badgeNumber"  disabled value="
<?php echo $_SESSION['accountId'];?>" class="UI-input" >
    </div>
  </div>
<?php }?>


  <p></p>
  <div class="UI-profile-email-div">
    <div>
      Emails
<?php if ($active) {?>
      <button class="UI-profile-update-button">Update Profile</button>
<?php }?>
    </div>
    <div class="UI-container">
      <input type="text" name="email1" id="email1" value="
<?php
echo $_SESSION['email'];
    ?>" class="UI-input" placeholder="Primary Email and Login (Required)">
      <input type="text" name="email2" value="
<?php
echo $_SESSION['accountInfo']['Email2'];
    ?>" class="UI-input" placeholder="Secondary Email">
      <input type="text" name="email3" value="
<?php
echo $_SESSION['accountInfo']['Email3'];
    ?>" class="UI-input" placeholder="Other Email">
    </div>
  </div>

  <p></p>
  <div class="UI-profile-phone-div">
    <div>
      Phone Numbers
<?php if ($active) {?>
      <button class="UI-profile-update-button">Update Profile</button>
<?php }?>
    </div>
    <div class="UI-container">
      <input type="text" name="phone1" value="<?php
        if (isset($_SESSION['accountInfo']['Phone'])) {
            echo $_SESSION['accountInfo']['Phone'];
        }
    ?>" class="UI-input UI-half" placeholder="Primary (Required)">
      <input type="text" name="phone2" value="
<?php
if (isset($_SESSION['accountInfo']['Phone2'])) {
    echo $_SESSION['accountInfo']['Phone2'];
}
    ?>" class="UI-input UI-half" placeholder="Other">
        <?php
        if (!empty($_SESSION['customFields']['currConComPos'])) {
            echo '<div class="UI-profile-concom-div">';
            echo '<span class="UI-profile-concom-label">Display Phone number on the ConCom list?</span>';
            echo '<select name="conComDisplayPhone" class="UI-profile-concom-select" required>';
            if (empty($_SESSION['accountInfo']['DisplayPhone'])) {
                echo '<option value="" disabled selected>Choose</option>';
            }
            echo '<option value="1"';
            if ($_SESSION['accountInfo']['DisplayPhone'] == "1") {
                echo ' selected';
            }
            echo '>Yes</option>';
            echo '<option value="0"';
            if ($_SESSION['accountInfo']['DisplayPhone'] == "0") {
                echo ' selected';
            }
            echo '>No</option>';
            echo '</select>';
            echo '</div>';
        }
        ?>
    </div>
  </div>

  <p></p>
  <div class="UI-profile-address-div">
    <div>
      Address
<?php if ($active) {?>
      <button class="UI-profile-update-button">Update Profile</button>
<?php }?>
    </div>
    <div class="UI-container">
      <input type="text" name="addressLine1" value="
<?php
echo $_SESSION['addr1'];
    ?>" class="UI-input" placeholder="Address (Required)">
      <input type="text" name="addressLine2" value="
<?php
echo $_SESSION['addr2'];
    ?>" class="UI-input">

      <input type="text" name="city" value="
<?php
echo $_SESSION['city'];
    ?>" class="UI-input UI-half" placeholder="City (Required)">
      <select name="state" onChange="ExpandOut(this.value, 'otherCountries')" class="UI-select UI-quarter">
        <option value=""<?php
        if (empty($_SESSION['state'])) {
            echo " selected";
        }
        echo '>Foreign (Non-US/Canada)</option>'."\n";

        require_once($FUNCTIONDIR.'/users.inc');

        $result = listStates();
        foreach ($result as $state) {
            echo '<option value="'.$state['code'].'"';
            if ($state['code'] == $_SESSION['state']) {
                echo " selected";
            }

            echo '>'.$state['code'].' - '.$state['name']."</option>\n";
        }
?>
      </select>
      <div class="UI-profile-zip-div">
        <input type="text" name="zipCode" value="
<?php
echo $_SESSION['zip'];
    ?>" class="UI-profile-zip-field" placeholder="Zip Code (Required)">
        <input type="text" name="zipPlus4" value="
<?php
if (isset($_SESSION['accountInfo']['AddressZipCodeSuffix'])) {
    echo $_SESSION['accountInfo']['AddressZipCodeSuffix'];
}
    ?>" class="UI-profile-zip-4-field" placeholder="Zip+4">
      </div>

      <select name="country" class="UI-select">
        <option value="">Select Country</option>
<?php
require_once($FUNCTIONDIR.'/users.inc');
$result = listCountries();

foreach ($result as $country) {
    echo '<option value="'.$country['id'].'"';
    if ($country['name'] == $_SESSION['accountInfo']['AddressCountry']) {
        echo " selected";
    }

    echo '>'.$country['name']."</option>\n";
}
?>
      </select>
      <div id="otherCountries" class="UI-hide">
        <input type="text" name="province" value="
<?php
if (isset($_SESSION['accountInfo']['AddressProvince'])) {
    echo $_SESSION['accountInfo']['AddressProvince'];
}
    ?>" class="UI-profile-province-field" placeholder="Province (Foreign Only)">
      </div>
    </div>
  </div>

  <p></p>
<?php if ($active) {?>
  <button class="UI-profile-update-button-end">Update Profile</button>
<?php } else { ?>
  <button type="button" class="UI-profile-update-button-end" onclick="createAccount();">Create Account</button>
<?php } ?>
</form>

<?php if ($active) {?>
<hr>
<h3>Your current security information</h3>
<div id="password_form" class="UI-container UI-padding">
  <div class="UI-profile-password-div">
    <div>
     Change Password
    </div>
    <div class="UI-container">
      <div class="UI-third">
        <input type="password" id="ciab_currentPassword" value="" class="UI-input" placeholder="Current Password (Required)">
      </div>
      <div class="UI-third">
        <input type="password" id="ciab_newPassword" value="" class="UI-input" placeholder="New Password(Required)">
      </div>
      <div class="UI-third">
        <input type="password" id="ciab_againPassword" value="" class="UI-input" placeholder="New Password Again(Required)">
      </div>
    </div>
  </div>
  <button class="UI-profile-update-password-button" onclick="changePassword();">Update Password</button>
</div>
<?php } ?>

<script>
function ExpandOut(val, id) {
    var x = document.getElementById(id);
    if (val === '') {
        if (x.className.indexOf("UI-show") == -1) {
            x.className += " UI-show";
        }
    } else {
        if (x.className.indexOf("UI-show") != -1) {
            x.className = x.className.replace(" UI-show", "");
        }
    }
}
</script>

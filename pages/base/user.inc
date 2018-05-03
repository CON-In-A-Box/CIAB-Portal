<?php

$active = (isset($_SESSION['accountId']));

// Dirty Hack - if country isn't set, default to the first country in the list
if (empty($_SESSION['accountInfo']['AddressCountry'])) {
    $_SESSION['accountInfo']['AddressCountry'] = 'United States';
}

// Dirty Hack - if state isn't set and provice is empty, default to Minnesota, because I can
if (empty($_SESSION['state']) && empty($_SESSION['accountInfo']['province'])) {
    $_SESSION['state'] = 'MN';
}
?>
<?php if ($active) { ?>
<h3>Your current profile information</h3>
<form action="index.php?Function=profile" method="post" onsubmit="return validateForm(this)" name="Profile" id="profile_update" class="w3-container">
<?php } else { ?>
<h3>New Account Information</h3>
<form name="Profile" id="profile_update" class="w3-container">
<?php } ?>

  <input type="hidden" name="accountId" value="<?php echo $_SESSION['accountId']; ?>">
  <div class="w3-lime w3-row">
    <div>
      Legal Name
<?php if ($active) {?>
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
<?php }?>
    </div>
    <div class="w3-container">
      <div class="w3-half">
        <div class="w3-twothird">
          <input type="text" name="firstName" value="<?php echo $_SESSION['firstName']; ?>" class="w3-input w3-border" placeholder="First (Required)">
        </div>

        <div class="w3-third">
          <input type="text" name="middleName" value="<?php echo $_SESSION['accountInfo']['MiddleName']; ?>" class="w3-input w3-border" placeholder="Middle">
        </div>
      </div>

      <div class="w3-half">
        <div class="w3-threequarter">
          <input type="text" name="lastName" value="<?php echo $_SESSION['lastName']; ?>" class="w3-input w3-border" placeholder="Last (Required)">
        </div>

        <div class="w3-quarter">
          <input type="text" name="suffix" value="<?php echo $_SESSION['accountInfo']['Suffix']; ?>" class="w3-input w3-border" placeholder="Suffix">
        </div>
      </div>
    </div>
  </div>

  <div class="w3-teal">
    <div>
      Preferred Name
<?php if ($active) {?>
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
<?php }?>
    </div>
    <div class="w3-container">
      <div class="w3-half">
        <input type="text" name="preferredFirstName" value="<?php
        if (isset($_SESSION['accountInfo']['PreferredFirstName'])) {
            echo $_SESSION['accountInfo']['PreferredFirstName'];
        }
?>" class="w3-input w3-border" placeholder="First - If Different">
      </div>

      <div class="w3-half">
        <input type="text" name="preferredLastName" value="<?php
        if (isset($_SESSION['accountInfo']['PreferredLastName'])) {
            echo $_SESSION['accountInfo']['PreferredLastName'];
        }
?>" class="w3-input w3-border" placeholder="Last - If Different">
      </div>
    </div>
  </div>

<?php if ($active) {?>
  <div class="w3-orange">
    <div>
      Badge Number
    </div>
    <div class="w3-container">
        <input type="text" name="badgeNumber"  disabled value="
<?php echo $_SESSION['accountId'];?>" class="w3-input w3-border" >
    </div>
  </div>
<?php }?>


  <p></p>
  <div class="w3-indigo">
    <div>
      Emails
<?php if ($active) {?>
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
<?php }?>
    </div>
    <div class="w3-container">
      <input type="text" name="email1" value="
<?php
echo $_SESSION['email'];
    ?>" class="w3-input w3-border" placeholder="Primary Email and Login (Required)">
      <input type="text" name="email2" value="
<?php
echo $_SESSION['accountInfo']['Email2'];
    ?>" class="w3-input w3-border" placeholder="Secondary Email">
      <input type="text" name="email3" value="
<?php
echo $_SESSION['accountInfo']['Email3'];
    ?>" class="w3-input w3-border" placeholder="Other Email">
    </div>
  </div>

  <p></p>
  <div class="w3-blue">
    <div>
      Phone Numbers
<?php if ($active) {?>
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
<?php }?>
    </div>
    <div class="w3-container">
      <input type="text" name="phone1" value="<?php
        if (isset($_SESSION['accountInfo']['Phone'])) {
            echo $_SESSION['accountInfo']['Phone'];
        }
    ?>" class="w3-input w3-border w3-half" placeholder="Primary (Required)">
      <input type="text" name="phone2" value="
<?php
if (isset($_SESSION['accountInfo']['Phone2'])) {
    echo $_SESSION['accountInfo']['Phone2'];
}
    ?>" class="w3-input w3-border w3-half" placeholder="Other">
        <?php
        if (isset($_SESSION['customFields']['currConComPos'])) {
            echo '<div class="w3-row">';
            echo '<span class="w3-right-align w3-col m7 l6">Display Phone number on the ConCom list?</span>';
            echo '<select name="conComDisplayPhone" class="w3-col m5 l6" required>';
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
  <div class="w3-purple">
    <div>
      Address
<?php if ($active) {?>
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
<?php }?>
    </div>
    <div class="w3-container">
      <input type="text" name="addressLine1" value="
<?php
echo $_SESSION['addr1'];
    ?>" class="w3-input w3-border" placeholder="Address (Required)">
      <input type="text" name="addressLine2" value="
<?php
echo $_SESSION['addr2'];
    ?>" class="w3-input w3-border">

      <input type="text" name="city" value="
<?php
echo $_SESSION['city'];
    ?>" class="w3-input w3-border w3-half" placeholder="City (Required)">
      <select name="state" onChange="ExpandOut(this.value, 'otherCountries')" class="w3-select w3-border w3-quarter">
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
      <div class="w3-quarter w3-row">
        <input type="text" name="zipCode" value="
<?php
echo $_SESSION['zip'];
    ?>" class="w3-input w3-border w3-right-align w3-col l8 m7 s7" placeholder="Zip Code (Required)">
        <input type="text" name="zipPlus4" value="
<?php
if (isset($_SESSION['accountInfo']['AddressZipCodeSuffix'])) {
    echo $_SESSION['accountInfo']['AddressZipCodeSuffix'];
}
    ?>" class="w3-input w3-border w3-col l4 m5 s5" placeholder="Zip+4">
      </div>

      <select name="country" class="w3-select w3-border">
        <option value="">Select Country</option>
<?php
require_once($FUNCTIONDIR.'/users.inc');
$result = listCountries();

foreach ($result as $country) {
    echo '<option ';
    if ($country['name'] == $_SESSION['accountInfo']['AddressCountry']) {
        echo " selected";
    }

    echo '>'.$country['name']."</option>\n";
}
?>
      </select>
      <div id="otherCountries" class="w3-hide">
        <input type="text" name="province" value="
<?php
if (isset($_SESSION['accountInfo']['AddressProvince'])) {
    echo $_SESSION['accountInfo']['AddressProvince'];
}
    ?>" class="w3-input w3-border w3-gray" placeholder="Province (Foreign Only)">
      </div>
    </div>
  </div>

  <p></p>
<?php if ($active) {?>
  <button class="w3-button w3-block w3-brown">Update Profile</button>
<?php } else { ?>
  <button type="button" class="w3-button w3-block w3-brown" onclick="createAccount();">Create Account</button>
<?php } ?>
</form>

<?php if ($active) {?>
<hr>
<h3>Your current security information</h3>
<div id="password_form" class="w3-container w3-padding">
  <div class="w3-red w3-row">
    <div>
     Change Password
    </div>
    <div class="w3-container">
      <div class="w3-third">
        <input type="password" id="ciab_currentPassword" value="" class="w3-input w3-border" placeholder="Current Password (Required)">
      </div>
      <div class="w3-third">
        <input type="password" id="ciab_newPassword" value="" class="w3-input w3-border" placeholder="New Password(Required)">
      </div>
      <div class="w3-third">
        <input type="password" id="ciab_againPassword" value="" class="w3-input w3-border" placeholder="New Password Again(Required)">
      </div>
    </div>
  </div>
  <button class="w3-button w3-block w3-red" onclick="changePassword();">Update Password</button>
</div>
<?php } ?>

<script>
function ExpandOut(val, id) {
    var x = document.getElementById(id);
    if (val === '') {
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
        }
    } else {
        if (x.className.indexOf("w3-show") != -1) {
            x.className = x.className.replace(" w3-show", "");
        }
    }
}
</script>
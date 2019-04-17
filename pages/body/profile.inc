<?php

// Going to need the actual fields for this
$preferredFirstName = search_definedFields('Preferred First Name');
$preferredLastName = search_definedFields('Preferred Last Name');
$cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');

// Dirty Hack - if country isn't set, default to the first country in the list
if (empty($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id'])) {
    $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id'] = 1;
}

// Dirty Hack - if state isn't set and provice is empty, default to Minnesota, because I can
if (empty($_SESSION['state']) && empty($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'])) {
    $_SESSION['state'] = 'MN';
}
?>
<div id="main_content"  class="w3-cell w3-cell-top UI-maincontent">
<h3>Your current profile information</h3>
<form action="index.php?Function=profile" method="post" onsubmit="return validate_form(this)" name="Profile" id="profile_update" class="UI-container">

  <input type="hidden" name="accountId" value="<?php echo $_SESSION['accountId']; ?>">
  <input type="hidden" name="contactId" value="<?php echo $_SESSION['contactId']; ?>">
<?php
if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressId'])) {
    echo '<input type="hidden" name="addressId" value="'.$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressId'].'">';
}
?>
  <div class="w3-lime w3-row">
    <div>
      Legal Name
      <button class="UI-button w3-brown UI-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="UI-container">
      <div class="w3-half">
        <div class="w3-twothird">
          <input type="text" name="firstName" value="<?php echo $_SESSION['firstName']; ?>" class="UI-input" placeholder="First (Required)">
        </div>

        <div class="w3-third">
          <input type="text" name="middleName" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['middleName']; ?>" class="UI-input" placeholder="Middle">
        </div>
      </div>

      <div class="w3-half">
        <div class="w3-threequarter">
          <input type="text" name="lastName" value="<?php echo $_SESSION['lastName']; ?>" class="UI-input" placeholder="Last (Required)">
        </div>
      
        <div class="w3-quarter">
          <input type="text" name="suffix" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['suffix']; ?>" class="UI-input" placeholder="Suffix">
        </div>
      </div>
    </div>
  </div>

  <div class="w3-teal">
    <div>
      Preferred Name
      <button class="UI-button w3-brown UI-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="UI-container">
      <div class="w3-half">
        <input type="text" name="preferredFirstName" value="<?php
        if (isset($_SESSION['customFields'][$preferredFirstName][0])) {
            echo $_SESSION['customFields'][$preferredFirstName][0];
        }
?>" class="UI-input" placeholder="First - If Different">
      </div>
        
      <div class="w3-half">
        <input type="text" name="preferredLastName" value="<?php
        if (isset($_SESSION['customFields'][$preferredLastName][0])) {
            echo $_SESSION['customFields'][$preferredLastName][0];
        }
?>" class="UI-input" placeholder="Last - If Different">
      </div>
    </div>
  </div>

  <div class="w3-orange">
    <div>
      Badge Number
    </div>
    <div class="UI-container">
        <input type="text" name="preferredLastName"  disabled value="
<?php echo $_SESSION['accountId'];?>" class="UI-input" >
    </div>
  </div>

  <p></p>
  <div class="w3-indigo">
    <div>
      Emails
      <button class="UI-button w3-brown UI-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="UI-container">
      <input type="text" name="email1" value="
<?php
echo $_SESSION['email'];
    ?>" class="UI-input" placeholder="Primary Email and Login (Required)">
      <input type="text" name="email2" value="
<?php
echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['email2'];
    ?>" class="UI-input" placeholder="Secondary Email">
      <input type="text" name="email3" value="
<?php
echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['email3'];
    ?>" class="UI-input" placeholder="Other Email">
    </div>
  </div>

  <p></p>
  <div class="w3-blue">
    <div>
      Phone Numbers
      <button class="UI-button w3-brown UI-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="UI-container">
      <input type="text" name="phone1" value="<?php
        if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'])) {
            echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'];
        }
    ?>" class="UI-input w3-half" placeholder="Primary (Required)">
      <input type="text" name="phone2" value="
<?php
if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['phone2'])) {
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['phone2'];
}
    ?>" class="UI-input w3-half" placeholder="Other">
        <?php
        if (isset($_SESSION['customFields']['currConComPos'])) {
            echo '<div class="w3-row">';
            echo '<span class="UI-right-align w3-col m7 l6">Display Phone number on the ConCom list?</span>';
            echo '<select name="conComDisplayPhone" class="w3-col m5 l6" required>';
            if (empty($_SESSION['customFields'][$cvgConComPhoneDisplay][0])) {
                echo '<option value="" disabled selected>Choose</option>';
            }
            echo '<option value="TRUE"';
            if (isset($_SESSION['customFields'][$cvgConComPhoneDisplay][0]) && $_SESSION['customFields'][$cvgConComPhoneDisplay][0] == "TRUE") {
                echo ' selected';
            }
            echo '>Yes</option>';
            echo '<option value="FALSE"';
            if (isset($_SESSION['customFields'][$cvgConComPhoneDisplay][0]) && $_SESSION['customFields'][$cvgConComPhoneDisplay][0] == "FALSE") {
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
      <button class="UI-button w3-brown UI-right" style="padding: 0px 16px;">Update Profile</button>
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
    ?>" class="UI-input w3-half" placeholder="City (Required)">
      <select name="state" onChange="ExpandOut(this.value, 'otherCountries')" class="UI-select w3-quarter">
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
    ?>" class="UI-input UI-right-align w3-col l8 m7 s7" placeholder="Zip Code (Required)">
        <input type="text" name="zipPlus4" value="
<?php
if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCodeSuffix'])) {
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCodeSuffix'];
}
    ?>" class="UI-input w3-col l4 m5 s5" placeholder="Zip+4">
      </div>
      
      <select name="country" class="UI-select">
        <option value="">Select Country</option>
<?php
require_once($FUNCTIONDIR.'/users.inc');
$result = listCountries();

foreach ($result as $country) {
    echo '<option value="'.$country['id'].'"';
    if ($country['id'] == $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id']) {
        echo " selected";
    }

    echo '>'.$country['name']."</option>\n";
}
?>
      </select>
      <div id="otherCountries" class="UI-hide">
        <input type="text" name="province" value="
<?php
if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'])) {
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'];
}
    ?>" class="UI-input w3-gray" placeholder="Province (Foreign Only)">
      </div>
    </div>
  </div>
  
  <p></p>
  <button class="UI-button w3-block w3-brown">Update Profile</button>
</form>

<hr>
<h3>Your current security information</h3>
<div id="password_form" class="UI-container UI-padding">
  <div class="UI-red w3-row">
    <div>
     Change Password
    </div>
    <div class="UI-container">
      <div class="w3-third">
        <input type="password" id="ciab_currentPassword" value="" class="UI-input" placeholder="Current Password (Required)">
      </div>
      <div class="w3-third">
        <input type="password" id="ciab_newPassword" value="" class="UI-input" placeholder="New Password(Required)">
      </div>
      <div class="w3-third">
        <input type="password" id="ciab_againPassword" value="" class="UI-input" placeholder="New Password Again(Required)">
      </div>
    </div>
  </div>
  <button class="UI-button w3-block UI-red" onclick="changePassword();">Update Password</button>
</div>

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
</div><!--close main_content div-->

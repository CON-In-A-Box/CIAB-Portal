<?php

// Going to need the actual fields for this
$preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
$preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);
$cvgConComPhoneDisplay = array_search('CVGConCom Publish Phone', $_SESSION['definedFields']);

// Dirty Hack - if country isn't set, default to the first country in the list
if (empty($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id'])) {
  $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id'] = 1;
}

// Dirty Hack - if state isn't set and provice is empty, default to Minnesota, because I can
if (empty($_SESSION['state']) && empty($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'])) {
  $_SESSION['state'] = 'MN';
}
?>
<div id="main_content"  class="w3-cell w3-cell-top w3-mobile">
<h3>Your current profile information</h3>
<form action="index.php?Function=profile" method="post" onsubmit="return validate_form(this)" name="Profile" id="profile_update" class="w3-container">

  <input type="hidden" name="accountId" value="<?php echo $_SESSION['accountId']; ?>">
  <input type="hidden" name="contactId" value="<?php echo $_SESSION['contactId']; ?>">
<?php
  if (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressId'])) {
    echo '<input type="hidden" name="addressId" value="' . $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressId'] . '">';
  }
?>
  <div class="w3-lime w3-row">
    <div>
      Legal Name
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="w3-container">
      <div class="w3-half">
        <div class="w3-twothird">
          <input type="text" name="firstName" value="<?php echo $_SESSION['firstName']; ?>" class="w3-input w3-border" placeholder="First (Required)">
        </div>

        <div class="w3-third">
          <input type="text" name="middleName" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['middleName']; ?>" class="w3-input w3-border" placeholder="Middle">
        </div>
      </div>

      <div class="w3-half">
        <div class="w3-threequarter">
          <input type="text" name="lastName" value="<?php echo $_SESSION['lastName']; ?>" class="w3-input w3-border" placeholder="Last (Required)">
        </div>
      
        <div class="w3-quarter">
          <input type="text" name="suffix" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['suffix']; ?>" class="w3-input w3-border" placeholder="Suffix">
        </div>
      </div>
    </div>
  </div>

  <div class="w3-teal">
    <div>
      Preferred Name
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="w3-container">
      <div class="w3-half">
        <input type="text" name="preferredFirstName" value="<?php
if(isset($_SESSION['customFields'][$preferredFirstName][0])) {
  echo $_SESSION['customFields'][$preferredFirstName][0];
}
?>" class="w3-input w3-border" placeholder="First - If Different">
      </div>
        
      <div class="w3-half">
        <input type="text" name="preferredLastName" value="<?php
if(isset($_SESSION['customFields'][$preferredLastName][0])) {
  echo $_SESSION['customFields'][$preferredLastName][0];
}
?>" class="w3-input w3-border" placeholder="Last - If Different">
      </div>
    </div>
  </div>

  <p></p>
  <div class="w3-indigo">
    <div>
      Emails
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="w3-container">
      <input type="text" name="email1" value="<?php echo $_SESSION['email']; ?>" class="w3-input w3-border" placeholder="Primary Email and Login (Required)">
      <input type="text" name="email2" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['email2']; ?>" class="w3-input w3-border" placeholder="Secondary Email">
      <input type="text" name="email3" value="<?php echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['email3']; ?>" class="w3-input w3-border" placeholder="Other Email">
    </div>
  </div>

  <p></p>
  <div class="w3-blue">
    <div>
      Phone Numbers
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="w3-container">
      <input type="text" name="phone1" value="<?php if(isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'])) { echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1']; } ?>" class="w3-input w3-border w3-half" placeholder="Primary (Required)">
      <input type="text" name="phone2" value="<?php if(isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['phone2'])) { echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['phone2']; } ?>" class="w3-input w3-border w3-half" placeholder="Other">
      <div class="w3-row">
        <span class="w3-center w3-col m7 l6">Display Phone number on the ConCom list?</span>
        <select name="conComDisplayPhone" class="w3-col m5 l6" required>
          <?php
          if(empty($_SESSION['customFields'][$cvgConComPhoneDisplay])) {
            echo '<option value="" disabled selected>Choose</option>';
          }
          echo '<option value="TRUE"';
          if(isset($_SESSION['customFields'][$cvgConComPhoneDisplay]) && $_SESSION['customFields'][$cvgConComPhoneDisplay] == "TRUE") {
            echo ' selected';
          }
          echo '>Yes</option>';
          echo '<option value="FALSE"';
          if(isset($_SESSION['customFields'][$cvgConComPhoneDisplay]) && $_SESSION['customFields'][$cvgConComPhoneDisplay] == "FALSE") {
            echo ' selected';
          }
          echo '>No</option>';
          ?>
        </select>
      </div>
    </div>
  </div>

  <p></p>
  <div class="w3-purple">
    <div>
      Address
      <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Update Profile</button>
    </div>
    <div class="w3-container">
      <input type="text" name="addressLine1" value="<?php echo $_SESSION['addr1']; ?>" class="w3-input w3-border" placeholder="Address (Required)">
      <input type="text" name="addressLine2" value="<?php echo $_SESSION['addr2']; ?>" class="w3-input w3-border">
      
      <input type="text" name="city" value="<?php echo $_SESSION['city']; ?>" class="w3-input w3-border w3-half" placeholder="City (Required)">
      <select name="state" onChange="ExpandOut(this.value, 'otherCountries')" class="w3-select w3-border w3-quarter">
        <option value=""<?php
if(empty($_SESSION['state'])) {
  echo " selected";
}
echo '>Foreign (Non-US/Canada)</option>' . "\n";

$request = [
  'method' => 'account/listStates',
];

$result = $GLOBALS['Neon']->go($request);
if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
  foreach ($result['states']['state'] as $line) {
    echo '<option value="' . $line['code'] . '"';
    if ($line['code'] == $_SESSION['state']) {
      echo " selected";
    }
    
    echo '>' . $line['code'] . ' - ' . $line['name'] . "</option>\n";
  }
  unset($line);
}
?>
      </select>
      <div class="w3-quarter w3-row">
        <input type="text" name="zipCode" value="<?php echo $_SESSION['zip']; ?>" class="w3-input w3-border w3-right-align w3-col l8 m7 s7" placeholder="Zip Code (Required)">
        <input type="text" name="zipPlus4" value="<?php if(isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCodeSuffix'])) { echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCodeSuffix']; } ?>" class="w3-input w3-border w3-col l4 m5 s5" placeholder="Zip+4">
      </div>
      
      <select name="country" class="w3-select w3-border">
        <option value="">Select Country</option>
<?php
$request = [
  'method' => 'account/listCountries',
];

$result = $GLOBALS['Neon']->go($request);

if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
  foreach ($result['countries']['country'] as $line) {
    echo '<option value="' . $line['id'] . '"';
    if ($line['id'] == $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['country']['id']) {
      echo " selected";
    }
    
    echo '>' . $line['name'] . "</option>\n";
  }
  unset($line);
}
?>
      </select>
      <div id="otherCountries" class="w3-hide">
        <input type="text" name="province" value="<?php if(isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'])) { echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province']; } ?>" class="w3-input w3-border w3-gray" placeholder="Province (Foreign Only)">
      </div>
    </div>
  </div>
  
  <p></p>
  <button class="w3-button w3-block w3-brown">Update Profile</button>
</form>
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

<script>
    userProfile.options({
        title: 'Your current profile information',
<?php
if (!empty($_SESSION['customFields']['currConComPos'])) {
    echo "panes: ['name', 'prefName', 'badge', 'emailAll', 'concomPhone', 'addr', 'password'],";
}
?>
    });

    var data = {
        'id':'<?php echo $_SESSION['accountId']; ?>',
        'email':'<?php echo $_SESSION['email'];  ?>',
        'legalFirstName':'<?php echo $_SESSION['firstName'];  ?>',
        'legalLastName':'<?php echo $_SESSION['lastName'];  ?>',
        'middleName':'<?php echo $_SESSION['accountInfo']['MiddleName'];  ?>',
        'suffix':'<?php echo $_SESSION['accountInfo']['Suffix'];  ?>',
        'email2':'<?php echo $_SESSION['accountInfo']['Email2'];  ?>',
        'email3':'<?php echo $_SESSION['accountInfo']['Email3'];  ?>',
        'phone1':'<?php echo $_SESSION['accountInfo']['Phone'];  ?>',
        'phone2':'<?php echo $_SESSION['accountInfo']['Phone2'];  ?>',
        'addressLine1':'<?php echo $_SESSION['addr1'];  ?>',
        'addressLine2':'<?php echo $_SESSION['addr2'];  ?>',
        'city':'<?php echo $_SESSION['city'];  ?>',
        'state':'<?php echo $_SESSION['accountInfo']['AddressState'];  ?>',
        'zipCode':'<?php echo $_SESSION['zip'];  ?>',
        'zipPlus4':'<?php echo $_SESSION['accountInfo']['AddressZipCodeSuffix'];  ?>',
        'countryName':'<?php echo $_SESSION['accountInfo']['AddressCountry'];  ?>',
        'province':'<?php echo $_SESSION['accountInfo']['AddressProvince'];  ?>',
        'preferredFirstName':'<?php echo $_SESSION['accountInfo']['PreferredFirstName'];  ?>',
        'preferredLastName':'<?php echo $_SESSION['accountInfo']['PreferredLastName'];  ?>',
        'conComDisplayPhone':'<?php echo $_SESSION['accountInfo']['DisplayPhone'];  ?>'
    };

    if (window.addEventListener) {
      window.addEventListener('load', function() {userProfile.populate(data);});
    } else {
      window.attachEvent('onload', function() {userProfile.populate(data);});
    }
</script>

<div id="main_content"  class="UI-cell-top UI-maincontent">
<div id="userProfile">
</div>
</div><!--close main_content div-->

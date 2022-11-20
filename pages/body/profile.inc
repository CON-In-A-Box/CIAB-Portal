<script>
    userProfile.options({
        title: 'Your current profile information',
<?php
if (!empty($_SESSION['customFields']['currConComPos'])) {
    echo "panes: ['name', 'prefName', 'badge', 'emailAll', 'concomPhone', 'addr', 'password'],";
} else {
    echo "panes: ['name', 'prefName', 'badge', 'emailAll', 'phone', 'addr', 'password'],";
}
?>
    });

    var data = {
        'id':'<?php echo $_SESSION['accountId']; ?>',
        'email':'<?php echo $_SESSION['email'];  ?>',
        'legal_first_name':'<?php echo $_SESSION['firstName'];  ?>',
        'legal_last_name':'<?php echo $_SESSION['lastName'];  ?>',
        'middle_name':'<?php echo $_SESSION['accountInfo']['MiddleName'];  ?>',
        'suffix':'<?php echo $_SESSION['accountInfo']['Suffix'];  ?>',
        'email2':'<?php echo $_SESSION['accountInfo']['Email2'];  ?>',
        'email3':'<?php echo $_SESSION['accountInfo']['Email3'];  ?>',
        'phone':'<?php echo $_SESSION['accountInfo']['Phone'];  ?>',
        'phone2':'<?php echo $_SESSION['accountInfo']['Phone2'];  ?>',
        'address_line1':'<?php echo $_SESSION['addr1'];  ?>',
        'address_line2':'<?php echo $_SESSION['addr2'];  ?>',
        'city':'<?php echo $_SESSION['city'];  ?>',
        'state':'<?php echo $_SESSION['accountInfo']['AddressState'];  ?>',
        'zip_code':'<?php echo $_SESSION['zip'];  ?>',
        'zip_plus4':'<?php echo $_SESSION['accountInfo']['AddressZipCodeSuffix'];  ?>',
        'country':'<?php echo $_SESSION['accountInfo']['AddressCountry'];  ?>',
        'province':'<?php echo $_SESSION['accountInfo']['AddressProvince'];  ?>',
        'preferred_first_name':'<?php echo $_SESSION['accountInfo']['PreferredFirstName'];  ?>',
        'preferred_last_name':'<?php echo $_SESSION['accountInfo']['PreferredLastName'];  ?>',
        'concom_display_phone':'<?php echo $_SESSION['accountInfo']['DisplayPhone'];  ?>',
        'pronouns':'<?php echo $_SESSION['accountInfo']['Pronouns'];  ?>'
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

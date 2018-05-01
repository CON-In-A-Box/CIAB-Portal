<?php
/*.
    require_module 'standard';
.*/
require_once($PAGESDIR.'/base/leftnav.inc');
require_once($FUNCTIONDIR.'/authentication.inc');
require_once(__DIR__."/../functions/admin.inc");
require_once($FUNCTIONDIR.'/users.inc');
?>
<div id="main_content"  class="w3-cell w3-cell-top w3-mobile">
<?php
echo "<h2 class='w3-center'>Welcome ".$_SESSION['preferredName']."!</h2>\n";

require_once(__dir__."/../functions/admin.inc");
require_once($FUNCTIONDIR.'/users.inc');

/* Retrieve and sanitize POST data */
$arguments = [
'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
'email'     => FILTER_SANITIZE_EMAIL,
'ID'        => FILTER_SANITIZE_SPECIAL_CHARS,

];
if (!empty($_POST)) {
    $searchCriteria = filter_input_array(INPUT_POST, $arguments);
} else {
    $searchCriteria = filter_input_array(INPUT_GET, $arguments);
}

// If there are search criteria present, execute the search query
if (!empty($searchCriteria['firstName']) ||
    !empty($searchCriteria['lastName']) ||
    !empty($searchCriteria['email'])) {
    $result = lookup_users_by_name_email(
        $searchCriteria['firstName'],
        $searchCriteria['lastName'],
        $searchCriteria['email'],
        ['AddressCity', 'AddressState']
    );
    $message = 'No results match your search.';
} else {
    $result = null;
    $message = 'You must specify search criteria.';
}

?>

<div id="left_bar" class="w3-content w3-left w3-margin">
<form action="index.php?Function=admin" method="POST" class="form-inline">
  <fieldset>
    <legend>Basic Account Search</legend>
    <p>
      <label>First Name</label>
      <input type="text" class="form-control" name="firstName" value="<?php echo htmlentities($searchCriteria['firstName']); ?>"/>
      <label>Last Name</label>
      <input type="text" class="form-control" name="lastName" value="<?php echo htmlentities($searchCriteria['lastName']); ?>" />
    </p><p>
      <label>Email</label>
      <input type="text" class="form-control" name="email" value="<?php echo htmlentities($searchCriteria['email']); ?>" />
    </p>
    <input type="submit" value="Search" class="btn btn-default" />
  </fieldset>
</form>
<hr>
    <form action="index.php?Function=admin" method="POST" class="form-inline">
    SUDO:
    <input type="text" class="form-control" name="SUDO" value=""
     placeholder="<account id>"/>
    <input type="submit" value="Execute" class="btn btn-default" />
    </form>
<hr>
    <form action="index.php?Function=admin" method="POST" class="form-inline">
    Set Account Temporary Password:<br>
    <input type="text" class="form-control" name="tmp_login" value=""
        placeholder="<account login>" autocomplete="off"/>
    <input type="text" class="form-control" name="tmp_passwd" value=
    "<?php echo rppg();?>" autocomplete="off"/>
    <input type="submit" value="Set Password" class="btn btn-default" />
    </form>
<hr>
    <input type="submit" value="Reload without Admin Priv" class="btn btn-default"
        onclick="removeAdmin();"/>
<hr>
<?php if ($result && !$result['code']) : ?>
  <table class="table table-striped">
    <tr>
      <th>Name</th>
      <th>Account ID</th>
      <th>Email</th>
      <th>Location</th>
    </tr>
    <?php foreach ($result['users'] as $r) : ?>
      <tr>
        <td><?php echo $r['First Name']; ?> <?php echo $r['Last Name']; ?></td>
        <td>
          <a href="index.php?Function=admin&ID=<?php echo $r['Id']; ?>" class="w3-button">
            <?php echo $r['Id']; ?>
          </a>
        </td>
        <td><?php echo $r['Email']; ?></td>
        <td><?php echo $r['AddressCity']; ?> <?php echo $r['AddressState']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else : ?>
  <p><?php echo $message; ?></p>
<?php
endif;

if (!empty($searchCriteria['ID'])) {
    unset($result);
    $result = pullIndividualAccount($searchCriteria['ID']);
    echo '<pre>';
    print_r($result);
    echo '</pre>';
}
?>
</div> <!-- left_bar --!>
<div id="right_bar" class="w3-right w3-content w3-margin">
<h2 class="w3-red w3-center">Site Configuration</h2>
<table class="w3-table w3-striped w3-border">
<tr><th class="w3-center">Field</th> <th class="w3-center">Value</th><th></th></tr>
<?php
$table = get_config();
foreach ($table as $line) {
    echo "<tr><td>".$line['Field']."</td>\n";
    echo "<td><input type='text' id=config_".$line['Field']." value=\"".$line['Value']."\" class='w3-input w3-border'></td>\n";
    echo "<td><button class='w3-button w3-round-xxlarge event-color-primary' onclick='setField(\"".$line['Field']."\")';>Set</button></td></tr>\n";
}
?>
    <tr><td>
      <input type='text' id=new_Field  value="" class='w3-input w3-border'>
    </td><td>
      <input type='text' id=new_Value  value="" class='w3-input w3-border'>
    </td><td>
      <button class='w3-button w3-round-xxlarge w3-red' onclick='addField()';>Add</button>
    </td></tr>
</table>
</div>
</div><!--close main_content div-->

<!--Play Area for random testing code -->
<div>
  <pre><?php
    // A simple place for admins to do random testing.

    ?></pre>
</div>
<!--End Play Area-->

<!-- Debug code for admins, just in case -->
<p></p>

<div id="dbg_info_content" class="w3-section w3-container">
  <button class="w3-button w3-small w3-block w3-left-align" onclick="expandSection('dbg_info');"><span>Debug Session Information&nbsp;</span><i id="dbg_info_arrow" class="fa fa-caret-down"></i></button>

  <div id="dbg_info" class="w3-section w3-margin w3-hide">
      <div id="DebugInfo" class="w3-section w3-small">
        <pre><?php print_r($_SESSION); ?></pre>
      </div>
  </div>
</div>

<!-- Activity log - limited to last lines, in reverse order -->
<p></p>
<div id="db_log_content" class="w3-section w3-container">
  <button class="w3-button w3-small w3-block w3-left-align" onclick="expandSection('db_log');"><span>DB Activity Log&nbsp;</span><i id="db_log_arrow" class="fa fa-caret-down"></i></button>

  <div id="db_log" class="w3-section w3-margin w3-hide">
   <div id="ActivityLog" class="w3-section" style="width: 100%;">
     <div>
       <button class="w3-button w3-tiny w3-left-align w3-yellow" onclick="downloadLog()">Download Full Activity Log (CSV)</button>
     </div>
        <?php dbPrintLog(1000); ?>
   </div>
  </div>
</div>

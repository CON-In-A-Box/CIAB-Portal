<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once __DIR__.'/../../functions/volunteer.inc';
require_once $FUNCTIONDIR.'/users.inc';
require_once $BASEDIR.'/console/console.inc';

console_body_data();

$uid = null;
$admin_mode = false;
if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'volunteerId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_GET, $arguments);
    $uid = $updateData['volunteerId'];
}

if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
    if (isset($_COOKIE["CIAB_VOLUNTEERADMIN"])) {
        $admin_mode = $_COOKIE["CIAB_VOLUNTEERADMIN"];
    }
}

if ($admin_mode && in_console()) {
    if (array_key_exists('VOL_ADMINKIOSK', $GLOBALS)) {
        $admin_mode = ($GLOBALS['VOL_ADMINKIOSK'] == '1');
    } else {
        $admin_mode = false;
    }
}

if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    $locked = array();
    foreach ($_SESSION['customFields']['currConComPos'] as $entry) {
        $locked[] = $entry['departmentId'];
    }
    $admin_mode = false;
}

echo "<script>\n";
echo "  var adminMode = ".json_encode($admin_mode).";\n";
echo "</script>\n";

?>

<div id="main_content">
  <div style="height:94vh;" :class="[isAdmin? 'UI-adminborder': '', 'VOL-main']">
    <div :class='[isAdmin ? "UI-admin-sectionbar" : "UI-event-sectionbar"]'>
        <span>Volunteer Administration</span>
    </div>

<div class="UI-rest UI-center">

<?php
if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
    if (!in_console() || (
            array_key_exists('VOL_ADMINKIOSK', $GLOBALS) &&
            $GLOBALS['VOL_ADMINKIOSK'])) {
        ?>
  <div>
    <div class="VOL-admin-switch">
      <div class='UI-table switch-table'>
        <div class='UI-table-row'>
          <div class='UI-table-cell'>
            Admin Mode
            <label class=switch><input type="checkbox" class=toggle id=admin_slider <?php
            if ($admin_mode) {
                echo "checked";
            }
            ?> onclick='toggleAdminMode();'>
              <div class=slider></div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
        <?php
    }

    generate_console_slider('volunteers');
}
?>

  <div id=user-lookup>
    <lookup-user message='Volunteer Badge Number, E-Mail or Full Name' url-tag='volunteerId'> </lookup-user>
  </div>
</div>

<div class="UI-rest UI-center">

<!------ Main Section ----->

<div class='VOL-row-padding'>
    <div id='info_div' class='UI-rest'>

<volunteer-hour-table ref=vhtbl styles="max-height:34vh; overflow:scroll;" :footer=true @hour-change="handleHourChange"></volunteer-hour-table>

<div v-if="!isAdmin && userId != null && $refs.gfttbl && $refs.gfttbl.loaded && $refs.gfttbl.getUnclaimedCount()[0] > 0" class='VOL-messages-div' style='max-height:10vh; overflow:scroll;'>
 <div id="volunteer_messages" class="VOL-messages">
    <ul>
        <li v-for="u in $refs.gfttbl.getUnclaimed()">
            <span v-if="u.reward_group">Free Group Gift </span>
            <span v-else>Free Gift </span>
            <b>{{u.name}}</b>
            <span> (at {{printHours(u.value)}}) is available and has not been acquired!</span>
            <span v-if="u.reward_group"><b> Pick 1 below!</b></span>
        </li>
    </ul>
  </div>
  <div v-if="$refs.gfttbl.getUnclaimedCount()[1] > 0" id='aquire_promo' class='UI-continer UI-padding UI-cell-middle'>
      <button v-if="$refs.gfttbl.getUnclaimedCount()[1] > 1" id='promo_check' class='UI-redbutton' @click="$refs.chkout.addPromoToCheckout($refs.gfttbl.getUnclaimed())">Claim items</button>
      <button v-else id='promo_check' class='UI-redbutton' @click="$refs.chkout.addPromoToCheckout($refs.gfttbl.getUnclaimed())">Claim item</button>
   </div>
</div>

<div v-if="isAdmin" class='VOL-admin' style='max-height:10vh; overflow:scroll;'>
    <div id='volunteer_admin_bar' class='VOL-admin-bar'>
    <button id='add_prize' class='UI-redbutton' @click='$refs.edprz.show(null)'>Add New Gift</button>
    <button id='gen_csv' class='UI-redbutton' onclick='window.location.href=\"index.php?Function=volunteers/report\";'>Go To Reports</button>
    </div>
</div>

<gift-table ref=gfttbl styles="max-height:34vh; overflow:scroll;" :footer=true v-bind:total-hours="totalHours" v-bind:hours-spent="totalSpentHours"></gift-table>

    </div>

    <edit-prize ref="edprz"></edit-prize>
    <edit-hours ref="edhrs"></edit-hours>
    <process-return ref="psrtn"></process-return>
    <checkout ref="chkout"></checkout>

</div>

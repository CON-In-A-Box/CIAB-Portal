<?php
/*.
    require_module 'standard';
.*/

require_once $FUNCTIONDIR.'/authentication.inc';
require_once $FUNCTIONDIR."/database.inc";
require_once $FUNCTIONDIR.'/users.inc';
require_once $BACKEND.'/CRM.inc';

const FIRST_NAME = 'firstName';
const LAST_NAME = 'lastName';
const EMAIL = 'email';
const ID = 'ID';
const FIELD = 'Field';
?>
<div id="main_content" class="UI-cell-top UI-maincontent">
    <?php
    echo "<h2 class='UI-sectionbar'>Welcome ".$_SESSION['preferredName']."!</h2>\n";

    /* Retrieve and sanitize POST data */
    $arguments = [
    FIRST_NAME => FILTER_SANITIZE_SPECIAL_CHARS,
    LAST_NAME => FILTER_SANITIZE_SPECIAL_CHARS,
    EMAIL => FILTER_SANITIZE_EMAIL,
    ID => FILTER_SANITIZE_SPECIAL_CHARS,

    ];
    if (!empty($_POST)) {
        $searchCriteria = filter_input_array(INPUT_POST, $arguments);
    } else {
        $searchCriteria = filter_input_array(INPUT_GET, $arguments);
    }

    // If there are search criteria present, execute the search query
    if (!empty($searchCriteria[FIRST_NAME])
        || !empty($searchCriteria[LAST_NAME])
        || !empty($searchCriteria[EMAIL])
    ) {
        $result = lookup_users_by_name_email(
            $searchCriteria[FIRST_NAME],
            $searchCriteria[LAST_NAME],
            $searchCriteria[EMAIL],
            ['AddressCity', 'AddressState']
        );
        $message = 'No results match your search.';
    } else {
        $result = null;
        $message = 'You must specify search criteria.';
    }

    ?>

    <div id="left_bar" class="ADMIN-center-bar">
        <form action="index.php?Function=admin" method="POST" class="form-inline">
            <fieldset>
<?php if (\ciab\CRM::active()) { ?>
                <legend>Basic CRM Account Search</legend>
<?php } else {?>
                <legend>Basic Account Search</legend>
<?php } ?>
                <p>
                    <label class='UI-label'>First Name</label>
                    <input type="text" class="form-control" name="firstName"
                           value="<?php echo htmlentities($searchCriteria[FIRST_NAME]); ?>"/>
                    <label class='UI-label'>Last Name</label>
                    <input type="text" class="form-control" name="lastName"
                           value="<?php echo htmlentities($searchCriteria[LAST_NAME]); ?>"/>
                </p>
                <p>
                    <label class='UI-label'>Email</label>
                    <input type="text" class="form-control" name="email"
                           value="<?php echo htmlentities($searchCriteria[EMAIL]); ?>"/>
                </p>
                <input type="submit" value="Search" class="UI-graybutton"/>
            </fieldset>
        </form>
        <hr>
        <form action="index.php?Function=admin" method="POST" class="form-inline" onsubmit="doSUDO();">
            SUDO:
            <input type="text" class="form-control" name="SUDO" value=""
                   placeholder="<account id>" id="sudo_id"/>
            <input type="submit" value="Execute" class="UI-graybutton"/>
        </form>
        <hr>
        <div>
            Set Account Password:<br>
            <input type="text" class="form-control" id="tmp_login" value=""
                   placeholder="<account login or id>" autocomplete="off"/>
            <input type="text" class="form-control" id="tmp_passwd" value="" autocomplete="off"/>
            <input type="button" value="Set Password" class="UI-graybutton" onclick="setPassword();"/>
        </div>
        <hr>
<?php if (\ciab\CRM::active()) { ?>
        <input type="submit" value="Manually Update Full Event Memberships from CRM (LONG)" class="UI-yellowbutton"
               onclick="updateMemberships();"/>
        <hr>
<?php } ?>
        <input type="submit" value="Reload without Admin Priv" class="UI-graybutton"
               onclick="removeAdmin();"/>
        <hr>
<?php if (\ciab\CRM::hasWebhooks()) { ?>
<h2 class="UI-blue UI-center">CRM Web Hooks</h2>
    <?php
    if (isset($GLOBALS['NEON_HOOK_NAME'])) {
        ?>
    <form action="index.php?Function=admin" method="POST" class="form-inline">
    Remove CRM Web Hooks:<br>
    <input type="text" class="form-control" name="hook_url" value="
        <?php echo $GLOBALS['NEON_HOOK_URL'];?>"
        autocomplete="off" readonly/>
    <input type="text" class="form-control" name="hook_name" value=
    "<?php echo $GLOBALS['NEON_HOOK_NAME'];?>" autocomplete="off" readonly/>
    <input type="hidden" name="hook_action" value="remove">
    <input type="submit" value="Remove Hooks" class="UI-graybutton" />
    </form>
        <?php
    } else {
        ?>
    <form action="index.php?Function=admin" method="POST" class="form-inline">
    Install CRM Web Hooks:<br>
    <input type="text" class="form-control" name="hook_url" value="<?php
    echo "http://".htmlspecialchars($_SERVER['HTTP_HOST']);?>"
        autocomplete="off"/>
    <input type="hidden" class="form-control" name="hook_name" value="" />
    <input type="hidden" name="hook_action" value="install">
    <input type="submit" value="Install Hooks" class="UI-graybutton" />
    </form>
        <?php
    }
    echo '<hr>';
}
?>
<input type="submit" value="Rebuild SCSS Style Sheets" class="UI-graybutton"
       onclick="rebuildSCSS();"/>
<hr>

        <?php if ($result && !$result['code']) : ?>
            <table class="table table-striped">
                <caption></caption>
                <tr>
                    <th id="NameHeader">Name</th>
                    <th id="AccountIDHeader">Account ID</th>
                    <th id="EmailHeader">Email</th>
                    <th id="LocationHeader">Location</th>
                </tr>
            <?php foreach ($result['users'] as $r) : ?>
                    <tr>
                        <td><?php echo $r['First Name']; ?><?php echo $r['Last Name']; ?></td>
                        <td>
                            <a href="index.php?Function=admin&ID=<?php echo $r['Id']; ?>" class="UI-roundbutton">
                <?php echo $r['Id']; ?>
                            </a>
                        </td>
                        <td><?php echo $r['Email']; ?></td>
                        <td><?php echo $r['AddressCity']; ?><?php echo $r['AddressState']; ?></td>
                    </tr>
            <?php endforeach; ?>
            </table>
        <?php else : ?>
            <p><?php echo $message; ?></p>
            <?php
        endif;

        if (!empty($searchCriteria[ID])) {
            unset($result);
            $result = pullIndividualAccount($searchCriteria[ID]);
            echo '<pre>';
            print_r($result);
            echo '</pre>';
        }
        ?>
    </div> <!-- left_bar -->
    <div id="right_bar" class="UI-right UI-content UI-margin">
        <h2 class="UI-red UI-center">Site Configuration</h2>

        <div id="settings" class="UI-table-all">
            <div class="UI-table-row">
                <div class="UI-table-cell UI-center">
                </div>
            </div>
        </div>
        <div class="UI-table-all">
            <div class="UI-table-header">
                <div class="UI-table-row">
                  <div class="UI-table-cell">
                    Field
                  </div>
                  <div class="UI-table-cell">
                  Value
                  </div>
                  <div class="UI-table-cell" id="SetHeader">
                   &nbsp;
                  </div>
                </div>
            </div>
            <div class="UI-table-row">
                <div class="UI-table-cell">
                    <input type='text' id=new_Field value="" class='UI-input'>
                </div>
                <div class="UI-table-cell">
                    <input type='text' id=new_Value value="" class='UI-input'>
                </div>
                <div class="UI-table-cell">
                    <button class='UI-redbutton' onclick='addField()' ;>Add</button>
                </div>
            </div>
        </div>
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

<div id="dbg_info_content" class="ADMIN-info-section">
    <button class="ADMIN-button" onclick="expandSection('dbg_info');"><span>Debug Session Information&nbsp;</span><em
                id="dbg_info_arrow" class="fas fa-caret-down"></em></button>

    <div id="dbg_info" class="ADMIN-info-parent UI-hide">
        <div id="DebugInfo" class="ADMIN-DebugInfo">
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    </div>
</div>

<!-- Activity log - limited to last lines, in reverse order -->
<p></p>
<div id="db_log_content" class="ADMIN-info-section">
    <button class="ADMIN-button" onclick="expandSection('db_log');getLog(1000);"><span>DB Activity Log&nbsp;</span><em
                id="db_log_arrow" class="fas fa-caret-down"></em></button>

    <div id="db_log" class="ADMIN-info-parent UI-hide">
        <div id="ActivityLog" class="ADMIN-ActivityLog">
            <div>
                <button class="ADMIN-download-button" onclick="downloadLog()">Download Full Activity Log (CSV)</button>
            </div>
            <div class="ADMIN-log-row">
                <div class="ADMIN-log-col-date">
                    Date
                </div>
                <div class="ADMIN-log-col-id">
                    AccountID
                </div>
                <div class="ADMIN-log-col-function">
                    Function
                </div>
                <div class="ADMIN-log-col-query">
                    Query
                </div>
            </div>
            <div id="LogEntries">
            </div>
        </div>
    </div>
</div>

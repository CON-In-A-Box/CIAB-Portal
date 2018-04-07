<?php

/*.
    require_module 'standard';
.*/

if (file_exists(__DIR__."/.ht_meetingsignin_config.php")) {
    header("Location: http://".$_SERVER['SERVER_NAME']."/index.php?Function=public");
}

$updateData = null;
$failed_message = "";
$tried = false;

/* Pre headers */
if (!empty($_POST)) {
    $arguments = [
    'DBHOST'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'DBUSER'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'DBNAME'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'DBPASS'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'DBPASS'   => FILTER_SANITIZE_SPECIAL_CHARS,

    'new_NEONID'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_NEONKEY'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_ADMINACCOUNTS' => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_ADMINEMAIL'    => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_CONHOST'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_TIMEZONE'      => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    $tried = true;
    $DBHOST = $updateData['DBHOST'];
    $DBUSER = $updateData['DBUSER'];
    $DBNAME = $updateData['DBNAME'];
    $DBPASS = $updateData['DBPASS'];

    $new_NEONID = $updateData['new_NEONID'];
    $new_NEONKEY = $updateData['new_NEONKEY'];
    $new_ADMINACCOUNTS = $updateData['new_ADMINACCOUNTS'];
    $new_CONHOST = $updateData['new_CONHOST'];
    $new_ADMINEMAIL = $updateData['new_ADMINEMAIL'];
    $new_TIMEZONE = $updateData['new_TIMEZONE'];


    if (strlen($DBHOST) > 0 &&
        strlen($DBUSER) > 0 &&
        strlen($DBNAME) > 0 &&
        strlen($DBPASS) > 0 &&
        strlen($new_NEONID) > 0 &&
        strlen($new_NEONKEY) > 0 &&
        strlen($new_ADMINACCOUNTS) > 0 &&
        strlen($new_CONHOST) > 0 &&
        strlen($new_ADMINEMAIL) > 0 &&
        strlen($new_TIMEZONE) > 0) {
        if (file_exists(__DIR__."/.ht_meetingsignin_config.php")) {
            chmod(__DIR__."/.ht_meetingsignin_config.php", 0600);
        }

        $myfile = fopen(__DIR__."/.ht_meetingsignin_config.php", "w");
        fwrite($myfile, <<<DONE
<?php
// CON-In-A-Box Site Config (module)
// Setup via web page

\$DB_BACKEND = "mysqlpdo.inc";

// DBConfig, passwords, etc
// Do not share this info
\$DBHOST = "$DBHOST"; // Where the DB Runs
\$DBUSER = "$DBUSER"; // The Database Username
\$DBNAME = "$DBNAME"; // The Database Name
\$DBPASS = "$DBPASS"; // *PLAIN TEXT* database user's password

DONE
        );
        fclose($myfile);
        chmod(__DIR__."/.ht_meetingsignin_config.php", 0400);

        $good = true;
        try {
            /* Load the database */
            require_once(__DIR__."/functions/functions.inc");
        } catch (Exception $e) {
            $good = false;
            $failed_message = "Failed to connect to Database";
        }

        if ($good) {
            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_CONHOST."' ";
            $sql .= "WHERE Field = 'CONHOST';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value =  '".$new_ADMINEMAIL."' ";
            $sql .= "WHERE Field = 'ADMINEMAIL';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_ADMINACCOUNTS."' ";
            $sql .= "WHERE Field = 'ADMINACCOUNTS';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_NEONID."' ";
            $sql .= "WHERE Field = 'NEONID';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_NEONKEY."' ";
            $sql .= "WHERE Field = 'NEONKEY';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_TIMEZONE."' ";
            $sql .= "WHERE Field = 'TIMEZONE';";
            DB::run($sql);

            header("Location: http://".$_SERVER['SERVER_NAME']."/index.php?Function=public");
            exit();
        }
    } else {
        $failed_message = "Fields left empty.";
    }
}

$CONSITENAME = "First Run Setup";
require(__DIR__.'/pages/base/header_start.inc');
require(__DIR__.'/pages/base/header_end.inc');
require(__DIR__.'/pages/base/body_begin.inc');

if (is_file(__DIR__."/.ht_meetingsignin_config.php")) {
    require_once(__DIR__."/.ht_meetingsignin_config.php");
}
?>

<h2 class="w3-center w3-green"> First Run Setup </h2>

<?php
if (strlen($failed_message)) {
    echo '<div class="w3-panel w3-padding-16 w3-red w3-red w3-center"><h2>'.$failed_message."</h2></div>\n";
}
?>

<div class="w3-panel w3-padding-16">
<p> Hello and welcome to Con-In-A-Box. This page will help to get your instance up and running. Please make sure all the information is correct before submitting. </p>
</div>

<div class="w3-panel w3-padding-16">
<i>Note: The Database and database user have to be created by hand before you begin the process. </i>
</div>

<div class="w3-panel w3-blue w3-center">
<p> In order to configure your site we need to know the following information. </p>
</div>

<hr>

<form action="configure_system.php" method="POST" class="w3-container">
    <label>Database Host:</label> <br>
    <input type="text" name="DBHOST" class="w3-input w3-border<?php
    if ($updateData != null && strlen($updateData['DBHOST'])) {
        echo '" value="'.$updateData['DBHOST'].'"';
    } elseif (isset($DBHOST)) {
        echo '" value="'.$DBHOST.'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Username:</label> <br>
    <input type="text" name="DBUSER" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['DBUSER'])) {
        echo '" value="'.$updateData['DBUSER'].'"';
    } elseif (isset($DBUSER)) {
        echo '" value="'.$DBUSER.'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Name:</label> <br>
    <input type="text" name="DBNAME" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['DBNAME'])) {
        echo '" value="'.$updateData['DBNAME'].'"';
    } elseif (isset($DBNAME)) {
        echo '" value="'.$DBNAME.'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Password:</label> <br>
    <input type="text" name="DBPASS" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['DBPASS'])) {
        echo '" value="'.$updateData['DBPASS'].'"';
    } elseif (isset($DBPASS)) {
        echo '" value="'.$DBPASS.'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <hr>

    <label>Neon Key:</label> <br>
    <input type="text" name="new_NEONKEY" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_NEONKEY'])) {
        echo '" value="'.$updateData['new_NEONKEY'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: bbbbbccccdddfae12341aabbccddeeff>">
    </br>

    <label>Neon ID:</label> <br>
    <input type="text" name="new_NEONID" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_NEONID'])) {
        echo '" value="'.$updateData['new_NEONID'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: home>">
    </br>

    <hr>

    <label>Event Host:</label> <br>
    <input type="text" name="new_CONHOST" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_CONHOST'])) {
        echo '" value="'.$updateData['new_CONHOST'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: AwesomeaCon>">
    </br>

    <label>Admin User IDs (comma seperated):</label> <br>
    <input type="text" name="new_ADMINACCOUNTS" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_ADMINACCOUNTS'])) {
        echo '" value="'.$updateData['new_ADMINACCOUNTS'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: 1234,5678,901234>">
    </br>

    <label>Admin Email:</label> <br>
    <input type="text" name="new_ADMINEMAIL" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_ADMINEMAIL'])) {
        echo '" value="'.$updateData['new_ADMINEMAIL'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: admin@host.con>">
    </br>

    <label>Timezone:</label> <br>
    <input type="text" name="new_TIMEZONE" class="w3-input w3-border <?php
    if ($updateData != null && strlen($updateData['new_TIMEZONE'])) {
        echo '" value="'.$updateData['new_TIMEZONE'].'"';
    } elseif ($tried) {
        echo 'w3-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: America/Chicago>">
    </br>

    <input type="submit" class="w3-button w3-green w3-center">
</form>

<?php
require(__DIR__.'/pages/base/body_end.inc');

<?php

/*.
    require_module 'standard';
.*/

require __DIR__."/functions/locations.inc";
require __DIR__."/vendor/autoload.php";
if (is_file(__DIR__.'/.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();

    $configure = false;
    try {
        $dotenv->required(['DBHOST', 'DBUSER', 'DBNAME', 'DBPASS', 'DB_BACKEND']);
    } catch (RuntimeException $e) {
        $configure = true;
    }
} else {
    $configure = true;
}

if (!$configure) {
    header("Location: ".$BASEURL."/index.php?Function=public");
}

$updateData = null;
$failed_message = "";
$tried = false;

/* Pre headers */
if (!empty($_POST)) {
    $arguments = [
    'DBHOST'   => FILTER_SANITIZE_RAW,
    'DBUSER'   => FILTER_SANITIZE_RAW,
    'DBNAME'   => FILTER_SANITIZE_RAW,
    'DBPASS'   => FILTER_SANITIZE_RAW,
    'DBPASS'   => FILTER_SANITIZE_RAW,

    'new_NEONID'        => FILTER_SANITIZE_RAW,
    'new_NEONKEY'       => FILTER_SANITIZE_RAW,
    'new_ADMINACCOUNTS' => FILTER_SANITIZE_RAW,
    'new_ADMINEMAIL'    => FILTER_SANITIZE_RAW,
    'new_NOREPLY_EMAIL' => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_FEEDBACK_EMAIL' => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_SECURITY_EMAIL' => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_HELP_EMAIL'    => FILTER_SANITIZE_SPECIAL_CHARS,
    'new_CONHOST'       => FILTER_SANITIZE_RAW,
    'new_TIMEZONE'      => FILTER_SANITIZE_RAW,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    $tried = true;
    $DBHOST = $updateData['DBHOST'];
    $DBUSER = $updateData['DBUSER'];
    $DBNAME = $updateData['DBNAME'];
    $DBPASS = $updateData['DBPASS'];

    $new_NEONID = $updateData['new_NEONID'];
    $new_NEONKEY = $updateData['new_NEONKEY'];
    $new_NEONBETA = (isset($_POST['new_NEONBETA'])) ? 1 : 0;
    $new_ADMINACCOUNTS = $updateData['new_ADMINACCOUNTS'];
    $new_CONHOST = $updateData['new_CONHOST'];
    $new_ADMINEMAIL = $updateData['new_ADMINEMAIL'];
    $new_FEEDBACK_EMAIL = $updateData['new_FEEDBACK_EMAIL'];
    $new_SECURITY_EMAIL = $updateData['new_SECURITY_EMAIL'];
    $new_HELP_EMAIL = $updateData['new_HELP_EMAIL'];
    $new_NOREPLY_EMAIL = $updateData['new_NOREPLY_EMAIL'];
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
        strlen($new_NOREPLY_EMAIL) > 0 &&
        strlen($new_TIMEZONE) > 0) {
        if (file_exists(__DIR__."/.env")) {
            chmod(__DIR__."/.env", 0600);
        }

        $myfile = fopen(__DIR__."/.env", "w");
        fwrite($myfile, <<<DONE
# CON-In-A-Box Site Config (module)
# Setup via web page

DB_BACKEND=mysqlpdo.inc

# DBConfig, passwords, etc
# Do not share this info
DBHOST="$DBHOST" # Where the DB Runs
DBUSER="$DBUSER" # The Database Username
DBNAME="$DBNAME" # The Database Name
DBPASS="$DBPASS" # *PLAIN TEXT* database user's password

DONE
        );
        fclose($myfile);
        chmod(__DIR__."/.env", 0400);

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

            $sql = "INSERT INTO `Configuration`(`Field`, `Value`)";
            $sql .= " VALUES ('NEONTRIAL', '".$new_NEONBETA."') ";
            $sql .= "ON DUPLICATE KEY UPDATE `Value` = '".$new_NEONBETA."';";
            DB::run($sql);

            $sql = "UPDATE `Configuration` SET ";
            $sql .= " Value = '".$new_TIMEZONE."' ";
            $sql .= "WHERE Field = 'TIMEZONE';";
            DB::run($sql);

            if (!empty($new_FEEDBACK_EMAIL)) {
                $sql = "INSERT INTO `Configuration`(`Field`, `Value`)";
                $sql .= " VALUES ('FEEDBACK_EMAIL', '".$new_FEEDBACK_EMAIL."') ";
                $sql .= "ON DUPLICATE KEY UPDATE `Value` = '".$new_FEEDBACK_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($new_SECURITY_EMAIL)) {
                $sql = "INSERT INTO `Configuration`(`Field`, `Value`)";
                $sql .= " VALUES ('SECURITY_EMAIL', '".$new_SECURITY_EMAIL."') ";
                $sql .= "ON DUPLICATE KEY UPDATE `Value` = '".$new_SECURITY_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($new_HELP_EMAIL)) {
                $sql = "INSERT INTO `Configuration`(`Field`, `Value`)";
                $sql .= " VALUES ('HELP_EMAIL', '".$new_HELP_EMAIL."') ";
                $sql .= "ON DUPLICATE KEY UPDATE `Value` = '".$new_HELP_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($new_NOREPLY_EMAIL)) {
                $sql = "INSERT INTO `Configuration`(`Field`, `Value`)";
                $sql .= " VALUES ('NOREPLY_EMAIL', '".$new_NOREPLY_EMAIL."') ";
                $sql .= "ON DUPLICATE KEY UPDATE `Value` = '".$new_NOREPLY_EMAIL."';";
                DB::run($sql);
            }

            header("Location: ".$BASEURL."/index.php?Function=public");
            exit();
        }
    } else {
        $failed_message = "Fields left empty.";
    }
}

$CONSITENAME = "First Run Setup";
require(__DIR__.'/pages/base/header_start.inc');
print "<link rel='stylesheet' href='style.php/styles.scss'/>";
require(__DIR__.'/pages/base/header_end.inc');
require(__DIR__.'/pages/base/body_begin.inc');
?>

<h2 class="UI-center UI-green"> First Run Setup </h2>

<?php
if (strlen($failed_message)) {
    echo '<div class="UI-configure-panel UI-red UI-center"><h2>'.$failed_message."</h2></div>\n";
}
?>

<div class="UI-configure-panel">
<p> Hello and welcome to Con-In-A-Box. This page will help to get your instance up and running. Please make sure all the information is correct before submitting. </p>
</div>

<div class="UI-configure-panel">
<i>Note: The Database and database user have to be created by hand before you begin the process. </i>
</div>

<div class="UI-configure-info-panel">
<p> In order to configure your site we need to know the following information. </p>
</div>

<hr>

<form action="configure_system.php" method="POST" class="UI-container">
    <label>Database Host:</label> <br>
    <input type="text" name="DBHOST" class="UI-input <?php
    if ($updateData != null && strlen($updateData['DBHOST'])) {
        echo '" value="'.$updateData['DBHOST'].'"';
    } elseif (isset($_ENV['DBHOST'])) {
        echo '" value="'.$_ENV['DBHOST'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Username:</label> <br>
    <input type="text" name="DBUSER" class="UI-input <?php
    if ($updateData != null && strlen($updateData['DBUSER'])) {
        echo '" value="'.$updateData['DBUSER'].'"';
    } elseif (isset($_ENV['DBUSER'])) {
        echo '" value="'.$_ENV['DBUSER'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Name:</label> <br>
    <input type="text" name="DBNAME" class="UI-input <?php
    if ($updateData != null && strlen($updateData['DBNAME'])) {
        echo '" value="'.$updateData['DBNAME'].'"';
    } elseif (isset($_ENV['DBNAME'])) {
        echo '" value="'.$_ENV['DBNAME'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <label>Database Password:</label> <br>
    <input type="text" name="DBPASS" class="UI-input <?php
    if ($updateData != null && strlen($updateData['DBPASS'])) {
        echo '" value="'.$updateData['DBPASS'].'"';
    } elseif (isset($_ENV['DBPASS'])) {
        echo '" value="'.$_ENV['DBPASS'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?>>
    </br>
    <hr>

    <label>Neon Key:</label> <br>
    <input type="text" name="new_NEONKEY" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_NEONKEY'])) {
        echo '" value="'.$updateData['new_NEONKEY'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: bbbbbccccdddfae12341aabbccddeeff>">
    </br>

    <label>Neon ID:</label> <br>
    <input type="text" name="new_NEONID" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_NEONID'])) {
        echo '" value="'.$updateData['new_NEONID'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: home>">
    </br>

    <label>Is Neon Trial Account:</label> <br>
    <input type="checkbox" name="new_NEONBETA" class="UI-checkbox <?php
    if ($updateData != null && $updateData['new_NEONBETA']) {
        echo '" checked"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?>>
    </br>

    <hr>

    <label>Event Host:</label> <br>
    <input type="text" name="new_CONHOST" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_CONHOST'])) {
        echo '" value="'.$updateData['new_CONHOST'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: AwesomeaCon>">
    </br>

    <label>Admin User IDs (comma seperated):</label> <br>
    <input type="text" name="new_ADMINACCOUNTS" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_ADMINACCOUNTS'])) {
        echo '" value="'.$updateData['new_ADMINACCOUNTS'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: 1234,5678,901234>">
    </br>

    <div class="UI-configure-panel UI-border">

    <label>Admin Email:</label> <br>
    <input type="text" name="new_ADMINEMAIL" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_ADMINEMAIL'])) {
        echo '" value="'.$updateData['new_ADMINEMAIL'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: admin@host.con>">
    </br>

    <label>No-Reply Email:</label> <br>
    <input type="text" name="new_NOREPLY_EMAIL" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_NOREPLY_EMAIL'])) {
        echo '" value="'.$updateData['new_NOREPLY_EMAIL'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
    ?> placeholder="<example: noreply@host.con>">
    </br>

    <label>Feedback Email: <span class='UI-configure-note'>Address for the feedback buttons and links. If unset, defaults to Admin Email address</span></label> <br>
    <input type="text" name="new_FEEDBACK_EMAIL" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_FEEDBACK_EMAIL'])) {
        echo '" value="'.$updateData['new_FEEDBACK_EMAIL'].'"';
    } else {
        echo '"';
    }
    ?> placeholder="<example: feedback@host.con>">
    </br>

    <label>Security Email: <span class='UI-configure-note'>This email recieves all the forgotten password request and other security notices. If unset, defaults to Admin Email address</span></label> <br>
    <input type="text" name="new_SECURITY_EMAIL" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_SECURITY_EMAIL'])) {
        echo '" value="'.$updateData['new_SECURITY_EMAIL'].'"';
    } else {
        echo '"';
    }
    ?> placeholder="<example: security@host.con>">
    </br>

    <label>Help Email: <span class='UI-configure-note'>Address for the help buttons and links. If unset, defaults to Admin Email address</span></label> <br>
    <input type="text" name="new_HELP_EMAIL" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_HELP_EMAIL'])) {
        echo '" value="'.$updateData['new_HELP_EMAIL'].'"';
    } else {
        echo '"';
    }
    ?> placeholder="<example: help@host.con>">
    </br>
    </div>

    <label>Timezone:</label> <br>
    <input type="text" name="new_TIMEZONE" class="UI-input <?php
    if ($updateData != null && strlen($updateData['new_TIMEZONE'])) {
        echo '" value="'.$updateData['new_TIMEZONE'].'"';
    } elseif ($tried) {
        echo 'UI-red"';
    } else {
        echo '"';
    }
?> placeholder="<example: America/Chicago>">
    </br>

    <input type="submit" class="UI-eventbutton UI-center">
</form>

<?php
require(__DIR__.'/pages/base/body_end.inc');

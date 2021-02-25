<?php
/*.
    require_module 'standard';
.*/

const ENV_FILE = __DIR__.'/.env';
const DB_HOST = 'DBHOST';
const DB_USER = 'DBUSER';
const DB_NAME = 'DBNAME';
const DB_PASS = 'DBPASS';
const NEW_NEONID = 'NEW_NEONID';
const NEW_NEONKEY = 'NEW_NEONKEY';
const NEW_NEONBETA = 'NEW_NEONBETA';
const NEW_ADMINACCOUNTS = 'NEW_ADMINACCOUNTS';
const NEW_ADMINEMAIL = 'NEW_ADMINEMAIL';
const NEW_NOREPLY_EMAIL = 'NEW_NOREPLY_EMAIL';
const NEW_FEEDBACK_EMAIL = 'NEW_FEEDBACK_EMAIL';
const NEW_SECURITY_EMAIL = 'NEW_SECURITY_EMAIL';
const NEW_HELP_EMAIL = 'NEW_HELP_EMAIL';
const NEW_ADMINCRED = 'NEW_ADMINPASSWORD';
const NEW_CONHOST = 'NEW_CONHOST';
const NEW_TIMEZONE = 'NEW_TIMEZONE';
const SQL_INSERT = "INSERT INTO `Configuration`(`Field`, `Value`)";
const SQL_ON_DUP = "ON DUPLICATE KEY UPDATE `Value` = '";
const VALUE_EQ = '" value="';
const UI_PROBLEM = 'UI-red';
const NEW_COLOR_PRIMARY = 'NEW_COLOR_PRIMARY';
const NEW_COLOR_PRIM_BACK = 'NEW_COLOR_PRIM_BACK';
const NEW_COLOR_SECONDARY = 'NEW_COLOR_SECONDARY';
const NEW_COLOR_SECOND_BACK = 'NEW_COLOR_SECOND_BACK';


function delete_files($target)
{
    if (is_dir($target)) {
        $files = glob($target.'*', GLOB_MARK);

        foreach ($files as $file) {
            delete_files($file);
        }

        @rmdir($target);
    } elseif (is_file($target)) {
        @unlink($target);
    }

}


require_once __DIR__."/functions/locations.inc";
require __DIR__."/vendor/autoload.php";
if (is_file(ENV_FILE)) {
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();

    $configure = false;
    try {
        $dotenv->required([DB_HOST, DB_USER, DB_NAME, DB_PASS, 'DB_BACKEND']);
    } catch (RuntimeException $e) {
        $configure = true;
    }

    if ($_ENV['DOCKER'] && !$configure) {
        require_once(__DIR__."/functions/functions.inc");
        $sql = "SELECT COUNT(`AccountID`) as count FROM `Authentication`;";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value === false || $value['count'] == 0) {
            $configure = true;
        }
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

/* Clear scss */
delete_files(@sys_get_temp_dir().'/scss_cache');

/* Pre headers */
if (!empty($_POST)) {
    $arguments = [
    DB_HOST   => FILTER_SANITIZE_SPECIAL_CHARS,
    DB_USER   => FILTER_SANITIZE_SPECIAL_CHARS,
    DB_NAME   => FILTER_SANITIZE_SPECIAL_CHARS,
    DB_PASS   => FILTER_SANITIZE_SPECIAL_CHARS,
    DB_PASS   => FILTER_SANITIZE_SPECIAL_CHARS,

    NEW_NEONID        => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_NEONKEY       => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_NEONBETA      => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_ADMINACCOUNTS => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_ADMINEMAIL    => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_NOREPLY_EMAIL => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_FEEDBACK_EMAIL => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_SECURITY_EMAIL => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_HELP_EMAIL    => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_ADMINCRED      => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_CONHOST       => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_TIMEZONE      => FILTER_SANITIZE_SPECIAL_CHARS,

    NEW_COLOR_PRIMARY => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_COLOR_PRIM_BACK => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_COLOR_SECONDARY => FILTER_SANITIZE_SPECIAL_CHARS,
    NEW_COLOR_SECOND_BACK => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    $tried = true;
    $DBHOST = $updateData[DB_HOST];
    $DBUSER = $updateData[DB_USER];
    $DBNAME = $updateData[DB_NAME];
    $DBPASS = $updateData[DB_PASS];

    $NEW_NEONID = $updateData[NEW_NEONID];
    $NEW_NEONKEY = $updateData[NEW_NEONKEY];
    $NEW_NEONBETA = $updateData[NEW_NEONBETA];
    $NEW_ADMINACCOUNTS = $updateData[NEW_ADMINACCOUNTS];
    $NEW_CONHOST = $updateData[NEW_CONHOST];
    $NEW_ADMINEMAIL = $updateData[NEW_ADMINEMAIL];
    $NEW_FEEDBACK_EMAIL = $updateData[NEW_FEEDBACK_EMAIL];
    $NEW_SECURITY_EMAIL = $updateData[NEW_SECURITY_EMAIL];
    $NEW_HELP_EMAIL = $updateData[NEW_HELP_EMAIL];
    $NEW_NOREPLY_EMAIL = $updateData[NEW_NOREPLY_EMAIL];
    $NEW_ADMINPASSWORD  = $updateData[NEW_ADMINCRED];
    $NEW_TIMEZONE = $updateData[NEW_TIMEZONE];

    $NEW_COLOR_PRIMARY = $updateData[NEW_COLOR_PRIMARY];
    $NEW_COLOR_PRIM_BACK = $updateData[NEW_COLOR_PRIM_BACK];
    $NEW_COLOR_SECONDARY = $updateData[NEW_COLOR_SECONDARY];
    $NEW_COLOR_SECOND_BACK = $updateData[NEW_COLOR_SECOND_BACK];

    if (strlen($DBHOST) > 0 &&
        strlen($DBUSER) > 0 &&
        strlen($DBNAME) > 0 &&
        strlen($DBPASS) > 0 &&
        strlen($NEW_CONHOST) > 0 &&
        strlen($NEW_ADMINEMAIL) > 0 &&
        strlen($NEW_NOREPLY_EMAIL) > 0 &&
        strlen($NEW_TIMEZONE) > 0 &&
        (strlen($NEW_NEONKEY) || strlen($NEW_ADMINPASSWORD))) {
        if (!isset($_ENV['DOCKER'])) {
            if (file_exists(ENV_FILE)) {
                chmod(ENV_FILE, 0600);
            }

            $myfile = fopen(ENV_FILE, "w");
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
            chmod(ENV_FILE, 0400);
        }

        $good = true;
        try {
            /* Load the database */
            require_once(__DIR__."/functions/functions.inc");
        } catch (Exception $e) {
            $good = false;
            $failed_message = "Failed to connect to Database";
        }

        if ($good) {
            if (strpos($NEW_ADMINEMAIL, '@') != false) {
                require_once(__DIR__."/functions/users.inc");
                require_once(__DIR__."/functions/authentication.inc");

                if (empty($NEW_NEONID) && empty($NEW_NEONKEY)) {
                    $aid = \createUser($NEW_ADMINEMAIL, 1000);
                    if ($aid !== null) {
                        \createPassword($NEW_ADMINEMAIL, $NEW_ADMINPASSWORD);
                        if (!empty($NEW_ADMINACCOUNTS)) {
                            $NEW_ADMINACCOUNTS = implode(',', [$NEW_ADMINACCOUNTS, $aid]);
                        } else {
                            $NEW_ADMINACCOUNTS = $aid;
                        }
                    } else {
                        $output = lookup_users_by_email($NEW_ADMINEMAIL);
                        if (count($output['users']) != 0) {
                            $aid = $output['users'][0]['Id'];
                        }
                    }
                }
            }

            $sql = SQL_INSERT;
            $sql .= " VALUES ('CONHOST', '".$NEW_CONHOST."') ";
            $sql .= SQL_ON_DUP.$NEW_CONHOST."';";
            DB::run($sql);

            if (!empty($NEW_ADMINEMAIL)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('ADMINEMAIL', '".$NEW_ADMINEMAIL."') ";
                $sql .= SQL_ON_DUP.$NEW_ADMINEMAIL."';";
                DB::run($sql);
            }

            if (!empty($NEW_ADMINACCOUNTS)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('ADMINACCOUNTS', '".$NEW_ADMINACCOUNTS."') ";
                $sql .= SQL_ON_DUP.$NEW_ADMINACCOUNTS."';";
                DB::run($sql);
            }

            if (!empty($NEW_NEONID)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('NEONID', '".$NEW_NEONID."') ";
                $sql .= SQL_ON_DUP.$NEW_NEONID."';";
                DB::run($sql);
            }

            if (!empty($NEW_NEONKEY)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('NEONKEY', '".$NEW_NEONKEY."') ";
                $sql .= SQL_ON_DUP.$NEW_NEONKEY."';";
                DB::run($sql);
            }

            if (!empty($NEW_NEONBETA)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('NEONTRIAL', '".$NEW_NEONBETA."') ";
                $sql .= SQL_ON_DUP.$NEW_NEONBETA."';";
                DB::run($sql);
            }

            $sql = SQL_INSERT;
            $sql .= " VALUES ('TIMEZONE', '".$NEW_TIMEZONE."') ";
            $sql .= SQL_ON_DUP.$NEW_TIMEZONE."';";
            DB::run($sql);

            if (!empty($NEW_FEEDBACK_EMAIL)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('FEEDBACK_EMAIL', '".$NEW_FEEDBACK_EMAIL."') ";
                $sql .= SQL_ON_DUP.$NEW_FEEDBACK_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($NEW_SECURITY_EMAIL)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('SECURITY_EMAIL', '".$NEW_SECURITY_EMAIL."') ";
                $sql .= SQL_ON_DUP.$NEW_SECURITY_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($NEW_HELP_EMAIL)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('HELP_EMAIL', '".$NEW_HELP_EMAIL."') ";
                $sql .= SQL_ON_DUP.$NEW_HELP_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($NEW_NOREPLY_EMAIL)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('NOREPLY_EMAIL', '".$NEW_NOREPLY_EMAIL."') ";
                $sql .= SQL_ON_DUP.$NEW_NOREPLY_EMAIL."';";
                DB::run($sql);
            }

            if (!empty($NEW_COLOR_PRIMARY)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('col.primary', '".$NEW_COLOR_PRIMARY."') ";
                $sql .= SQL_ON_DUP.$NEW_COLOR_PRIMARY."';";
                DB::run($sql);
            }

            if (!empty($NEW_COLOR_PRIM_BACK)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('col.prim-back', '".$NEW_COLOR_PRIM_BACK."') ";
                $sql .= SQL_ON_DUP.$NEW_COLOR_PRIM_BACK."';";
                DB::run($sql);
            }

            if (!empty($NEW_COLOR_SECONDARY)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('col.secondary', '".$NEW_COLOR_SECONDARY."') ";
                $sql .= SQL_ON_DUP.$NEW_COLOR_SECONDARY."';";
                DB::run($sql);
            }

            if (!empty($NEW_COLOR_SECOND_BACK)) {
                $sql = SQL_INSERT;
                $sql .= " VALUES ('col.second-back', '".$NEW_COLOR_SECOND_BACK."') ";
                $sql .= SQL_ON_DUP.$NEW_COLOR_SECOND_BACK."';";
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
print "<link rel='stylesheet' href='style.php/styles.scss'/>\n";
print "<script src='sitesupport/common.js'></script>\n";
print "<script src='sitesupport/dropdownsection.js'></script>\n";
require(__DIR__.'/pages/base/header_end.inc');
require(__DIR__.'/pages/base/body_begin.inc');
?>

<div id="page" class="UI-container">
    <div class="UI-maincontent">
    <h2 class="UI-center event-color-primary"> First Run Setup </h2>

    <?php
    if (strlen($failed_message)) {
        echo '<div class="UI-configure-panel UI-red UI-center"><h2>'.$failed_message."</h2></div>\n";
    }
    ?>

    <div class="UI-configure-panel">
    <p> Hello and welcome to Con-In-A-Box. This page will help to get your instance up and running. Please make sure all the information is correct before submitting. </p>
    </div>

    <div class="UI-configure-panel">
    <em>Note: The Database and database user have to be created by hand before you begin the process. </em>
    </div>

    <div class="UI-configure-info-panel">
    <p> In order to configure your site we need to know the following information. </p>
    </div>

    <form action="configure_system.php" method="POST" class="UI-container">

    <div class="UI-event-sectionbar UI-margin">Database Configuration (Required)</div>
    <div class="UI-margin UI-padding UI-border">
        <div class="UI-pad-bottom">
            <label>Database Host:</label>
            <input type="text" name="DBHOST" class="UI-input <?php
            if ($updateData != null && strlen($updateData[DB_HOST])) {
                echo VALUE_EQ.$updateData[DB_HOST];
            } elseif (isset($_ENV[DB_HOST])) {
                echo VALUE_EQ.$_ENV[DB_HOST];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>">
        </div>
        <div class="UI-pad-bottom">
            <label>Database Username:</label> <br>
            <input type="text" name="DBUSER" class="UI-input <?php
            if ($updateData != null && strlen($updateData[DB_USER])) {
                echo VALUE_EQ.$updateData[DB_USER];
            } elseif (isset($_ENV[DB_USER])) {
                echo VALUE_EQ.$_ENV[DB_USER];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>">
        </div>
        <div class="UI-pad-bottom">
            <label>Database Name:</label> <br>
            <input type="text" name="DBNAME" class="UI-input <?php
            if ($updateData != null && strlen($updateData[DB_NAME])) {
                echo VALUE_EQ.$updateData[DB_NAME];
            } elseif (isset($_ENV[DB_NAME])) {
                echo VALUE_EQ.$_ENV[DB_NAME];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>">
        </div>
        <div class="UI-pad-bottom">
            <label>Database Password:</label> <br>
            <input type="text" name="DBPASS" class="UI-input <?php
            if ($updateData != null && strlen($updateData[DB_PASS])) {
                echo VALUE_EQ.$updateData[DB_PASS];
            } elseif (isset($_ENV[DB_PASS])) {
                echo VALUE_EQ.$_ENV[DB_PASS];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>">
        </div>
    </div>

    <div id="neon_content" class="UI-margin">
        <button type="button" class="UI-event-dropdown-bar" onclick="expandSection('neon')">
            <span>Neon CRM Data (Optional)</span> <em id="neon_arrow" class="fas fa-caret-down"></em>
        </button>

        <div id="neon" class="UI-container UI-padding UI-adminborder UI-hide">
            <div class="UI-orange UI-configure-info-panel">
                Use of NEON is optional. Define BOTH keys if NEON is in use.
            </div>

            <div>
                <label>Neon Key:</label> <br>
                <input type="text" name="NEW_NEONKEY" class="UI-input <?php
                if ($updateData != null && strlen($updateData[NEW_NEONKEY])) {
                    echo VALUE_EQ.$updateData[NEW_NEONKEY];
                } elseif ($tried) {
                    echo UI_PROBLEM;
                }
                ?>" placeholder="<example: bbbbbccccdddfae12341aabbccddeeff>">
                </br>

                <label>Neon ID:</label> <br>
                <input type="text" name="NEW_NEONID" class="UI-input <?php
                if ($updateData != null && strlen($updateData[NEW_NEONID])) {
                    echo VALUE_EQ.$updateData[NEW_NEONID];
                } elseif ($tried) {
                    echo UI_PROBLEM;
                }
                ?>" placeholder="<example: home>">
                </br>

                <label>Is Neon Trial Account:</label> <br>
                <input type="checkbox" name="NEW_NEONBETA" class="UI-checkbox <?php
                if ($updateData != null && $updateData[NEW_NEONBETA]) {
                    echo '" checked';
                } elseif ($tried) {
                    echo UI_PROBLEM;
                }
                ?>">
                </br>

                <div class="w3-orange w3-panel w3-padding-16">
                If you are importing from NEON put a comma seperated list of user IDs of the primary admins here. If we do not find an '@' in the entry we will just add the contents here as the admin Ids being imported.
                </div>
                <label>Admin User IDs (comma seperated):</label> <br>
                <input type="text" name="NEW_ADMINACCOUNTS" class="UI-input <?php
                if ($updateData != null && strlen($updateData[NEW_ADMINACCOUNTS])) {
                    echo VALUE_EQ.$updateData[NEW_ADMINACCOUNTS];
                } elseif ($tried) {
                    echo UI_PROBLEM;
                }
                ?>" placeholder="<example: 1234,5678,901234>">
            </div>
        </div>
    </div>

    <div class="UI-event-sectionbar UI-margin">Event Configuration (Required)</div>
    <div class="UI-margin UI-configure-panel UI-border">
        <div class="UI-pad-bottom">
            <label>Event Host:</label> <br>
            <input type="text" name="NEW_CONHOST" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_CONHOST])) {
                echo VALUE_EQ.$updateData[NEW_CONHOST];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>" placeholder="<example: AwesomeaCon>">
        </div>
        <div class="UI-pad-bottom">
            <label>Timezone:</label> <br>
            <input type="text" name="NEW_TIMEZONE" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_TIMEZONE])) {
                echo VALUE_EQ.$updateData[NEW_TIMEZONE];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>" placeholder="<example: America/Chicago>">
        </div>
        <div class="UI-pad-bottom">
         <label for="NEW_COLOR_PRIMARY">Primary Banner Text Color:</label>
         <input type="color" name="NEW_COLOR_PRIMARY<?php
            if ($updateData != null && strlen($updateData[NEW_COLOR_PRIMARY])) {
                echo VALUE_EQ.$updateData[NEW_COLOR_PRIMARY];
            } else {
                echo "\" value=\"#FFFFFF";
            }
            ?>">
        </div>
        <div class="UI-pad-bottom">
         <label for="NEW_COLOR_PRIM_BACK">Primary Banner Color:</label>
         <input type="color" name="NEW_COLOR_PRIM_BACK<?php
            if ($updateData != null && strlen($updateData[NEW_COLOR_PRIM_BACK])) {
                echo VALUE_EQ.$updateData[NEW_COLOR_PRIM_BACK];
            } else {
                echo "\" value=\"#4CAF50";
            }
            ?>">
        </div>
        <div class="UI-pad-bottom">
         <label for="NEW_COLOR_SECONDARY">Secondary Banner Text Color:</label>
         <input type="color" name="NEW_COLOR_SECONDARY<?php
            if ($updateData != null && strlen($updateData[NEW_COLOR_SECONDARY])) {
                echo VALUE_EQ.$updateData[NEW_COLOR_SECONDARY];
            } else {
                echo "\" value=\"#FFFFFF";
            }
            ?>">
        </div>

        <div class="UI-pad-bottom">
         <label for="NEW_COLOR_SECOND_BACK">Secondary Banner Color:</label>
         <input type="color" name="NEW_COLOR_SECOND_BACK<?php
            if ($updateData != null && strlen($updateData[NEW_COLOR_SECOND_BACK])) {
                echo VALUE_EQ.$updateData[NEW_COLOR_SECOND_BACK];
            } else {
                echo "\" value=\"#2196F3";
            }
            ?>">

        </div>
    </div>

    <div class="UI-event-sectionbar UI-margin">Admin Configuration (Required, If not using Neon)</div>
    <div class="UI-configure-panel UI-border UI-margin">
        <div class="UI-pad-bottom">
            <label>Admin Email: <span class="UI-configure-note">Account will be created</span></label> <br>
            <input type="text" name="NEW_ADMINEMAIL" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_ADMINEMAIL])) {
                echo VALUE_EQ.$updateData[NEW_ADMINEMAIL];
            } elseif ($tried && empty($NEW_NEONKEY)) {
                echo UI_PROBLEM;
            }
            ?>" placeholder="<example: admin@host.con>">
        </div>
        <div class="UI-pad-bottom">
            <label>Admin Password: <span class="UI-configure-note">Required.</span></label> <br>
            <input type="text" name="NEW_ADMINPASSWORD" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_ADMINCRED])) {
                echo VALUE_EQ.$updateData[NEW_ADMINCRED];
            } elseif ($tried && empty($NEW_NEONKEY)) {
                echo UI_PROBLEM;
            }
            ?>" placeholder="<example: aabbccddee>">
        </div>
    </div>

    <div class="UI-event-sectionbar UI-margin">Email Configuration (Required)</div>
    <div class="UI-margin UI-configure-panel UI-border">
        <div class="UI-pad-bottom">
            <label>No-Reply Email:</label> <br>
            <input type="text" name="NEW_NOREPLY_EMAIL" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_NOREPLY_EMAIL])) {
                echo VALUE_EQ.$updateData[NEW_NOREPLY_EMAIL];
            } elseif ($tried) {
                echo UI_PROBLEM;
            }
            ?>" placeholder="<example: noreply@host.con>">
        </div>

        <div class="UI-pad-bottom">
            <label>Feedback Email: <span class='UI-configure-note'>Address for the feedback buttons and links. If unset, defaults to Admin Email address</span></label> <br>
            <input type="text" name="NEW_FEEDBACK_EMAIL" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_FEEDBACK_EMAIL])) {
                echo VALUE_EQ.$updateData[NEW_FEEDBACK_EMAIL].'"';
            } else {
                echo '"';
            }
            ?> placeholder="<example: feedback@host.con>">
        </div>

        <div class="UI-pad-bottom">
            <label>Security Email: <span class='UI-configure-note'>This email recieves all the forgotten password request and other security notices. If unset, defaults to Admin Email address</span></label> <br>
            <input type="text" name="NEW_SECURITY_EMAIL" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_SECURITY_EMAIL])) {
                echo VALUE_EQ.$updateData[NEW_SECURITY_EMAIL].'"';
            } else {
                echo '"';
            }
            ?> placeholder="<example: security@host.con>">
        </div>

        <div class="UI-pad-bottom">
            <label>Help Email: <span class='UI-configure-note'>Address for the help buttons and links. If unset, defaults to Admin Email address</span></label> <br>
            <input type="text" name="NEW_HELP_EMAIL" class="UI-input <?php
            if ($updateData != null && strlen($updateData[NEW_HELP_EMAIL])) {
                echo VALUE_EQ.$updateData[NEW_HELP_EMAIL].'"';
            } else {
                echo '"';
            }
            ?> placeholder="<example: help@host.con>">
        </div>
    </div>

    <div class='UI-center'>
        <input type="submit" class="UI-eventbutton UI-center">
    </div>
    </form>
</div>


<?php
require(__DIR__.'/pages/base/body_end.inc');

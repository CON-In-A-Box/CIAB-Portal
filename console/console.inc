<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

function in_console()
{
    return (array_key_exists('consoleMode', $_SESSION) && isset($_SESSION['consoleMode']));

}


function is_console($type)
{
    return (array_key_exists('consoleMode', $_SESSION) && $_SESSION['consoleMode'] == $type);

}


function get_console()
{
    if (in_console()) {
        return $_SESSION['consoleMode'];
    } else {
        return null;
    }

}


function console_user()
{
    if (!isset($_SESSION['consoleUser'])) {
        return null;
    } else {
        return $_SESSION['consoleUser'];
    }

}


function set_console_user($user)
{
    if (!in_console()) {
        return;
    }
    $_SESSION['consoleUser'] = $user;

}


function leave_console()
{
    unset($_SESSION['consoleUser']);
    unset($_SESSION['consoleMode']);

}


function enter_console($type, $user = null)
{
    $_SESSION['consoleMode'] = $type;
    if ($user !== null) {
        $_SESSION['consoleUser'] = $user;
    }

}


function generate_console_slider($type)
{
?>
  <div>
    <div class="UI-console-slider">
      <div class='UI-table switch-table'>
        <div class='UI-table-row'>
          <div class='UI-table-cell'>
            Console Mode
            <label class=switch><input type="checkbox" class=toggle id=console_slider <?php
            if (is_console($type)) {
                echo "checked";
            }
?> onclick='toggleConsoleMode("<?php echo $type;?>");'>
            <div class=slider></div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
echo "  <script>\n";
echo "  var consoleMode = ".json_encode(is_console($type)).";\n";
echo "  </script>\n";

}


function generate_console_menus($type)
{
    global $base_menus;
    if (is_console($type)) {
        $base_menus[] = array('base style' => 'UI-right event-secondary-color',
         'selected style' => 'event-color-primary UI-right',
         'function' => $type.'/console',
         'title' => 'Change Console User from '.console_user(),
         'i class' => 'fas fa-exchange-alt',
         'text' => 'Change Console User from '.console_user(),
         'responsive' => true);
    }

}


function console_body_data()
{
    echo "<script>\n";
    echo "  var userEmail = '".$_SESSION['email']."';\n";
    echo "</script>\n";

}


function console_head()
{
    echo "<script src='console/console.js'></script>\n";
    echo "<script src='sitesupport/common.js'></script>\n";
    echo "<script src='sitesupport/authentication.js'></script>\n";
    echo "<link rel='stylesheet' type='text/css' href='sitesupport/slider.css' />\n";

}

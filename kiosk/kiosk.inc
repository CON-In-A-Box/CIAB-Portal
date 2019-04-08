<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

function in_kiosk()
{
    return (array_key_exists('kioskMode', $_SESSION) && isset($_SESSION['kioskMode']));

}


function is_kiosk($type)
{
    return (array_key_exists('kioskMode', $_SESSION) && $_SESSION['kioskMode'] == $type);

}


function get_kiosk()
{
    if (in_kiosk()) {
        return $_SESSION['kioskMode'];
    } else {
        return null;
    }

}


function kiosk_user()
{
    if (!isset($_SESSION['kioskUser'])) {
        return null;
    } else {
        return $_SESSION['kioskUser'];
    }

}


function set_kiosk_user($user)
{
    if (!in_kiosk()) {
        return;
    }
    $_SESSION['kioskUser'] = $user;

}


function leave_kiosk()
{
    unset($_SESSION['kioskUser']);
    unset($_SESSION['kioskMode']);

}


function enter_kiosk($type, $user = null)
{
    $_SESSION['kioskMode'] = $type;
    if ($user !== null) {
        $_SESSION['kioskUser'] = $user;
    }

}


function generate_kiosk_slider($type)
{
?>
  <div>
    <div class="w3-show-inline-block w3-left event-color-secondary UI-margin">
      <table class='switch-table'><tr><td>
        Kiosk Mode
        <label class=switch><input type="checkbox" class=toggle id=kiosk_slider <?php
        if (is_kiosk($type)) {
            echo "checked";
        }
?> onclick='toggleKioskMode("<?php echo $type;?>");'>
          <div class=slider></div></label></td></tr>
      </table>
    </div>
  </div>

<?php
echo "  <script>\n";
echo "  var kioskMode = ".json_encode(is_kiosk($type)).";\n";
echo "  </script>\n";

}


function generate_kiosk_menus($type)
{
    global $base_menus;
    if (is_kiosk($type)) {
        $base_menus[] = array('base style' => 'w3-right event-secondary-color',
         'selected style' => 'event-color-primary w3-right',
         'function' => $type.'/kiosk',
         'title' => 'Change Kiosk User from '.kiosk_user(),
         'i class' => 'fa fa-exchange',
         'text' => 'Change Kiosk User from '.kiosk_user(),
         'responsive' => true);
    }

}


function kiosk_body_data()
{
    echo "<script>\n";
    echo "  var userEmail = '".$_SESSION['email']."';\n";
    echo "</script>\n";

}


function kiosk_head()
{
    echo "<script src='kiosk/kiosk.js'></script>\n";
    echo "<script src='sitesupport/authentication.js'></script>\n";
    echo "<link rel='stylesheet' type='text/css' href='sitesupport/slider.css' />\n";

}

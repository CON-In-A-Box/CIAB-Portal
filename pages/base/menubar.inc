<script>
  function myMenuBar() {
    var x = document.getElementById("main_nav_list");
    if (x.className.indexOf("w3-show") == -1) {
      x.className += " w3-show";
    } else { 
      x.className = x.className.replace(" w3-show", "");
    }
  }
</script>
<div class="w3-top">
  <div id="main_nav" class="w3-bar w3-dark-grey">

<?php

if (!isset($base_styles)) {
    $base_styles = " w3-bar-item w3-button ";
}
if (!isset($resp_styles)) {
    $resp_styles = " w3-hide-small w3-hide-medium ";
}

if (!isset($base_menus)) {
    $base_menus = array();
    $base_menus[] = array('base style' => '',
         'selected style' => 'w3-green',
         'function' => 'main',
         'title' => 'Main Page',
         'i class' => 'fa fa-home',
         'text' => 'Main',
         'responsive' => false);

    $base_menus[] = array('base style' => '',
         'selected style' => 'w3-green',
         'function' => 'profile',
         'title' => 'Update Profile Information',
         'i class' => 'fa fa-user',
         'text' => 'Profile',
         'responsive' => true);
}

if (!isset($admin_menus)) {
    $admin_menus = [
    ];
}

    $modules = scandir($MODULESDIR);
    foreach ($modules as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (in_array($value, $DISABLEDMODULES)) {
                continue;
            }
            if (is_dir($MODULESDIR . DIRECTORY_SEPARATOR . $value)) {
                if (is_file($MODULESDIR . DIRECTORY_SEPARATOR . $value . DIRECTORY_SEPARATOR . 'pages/menubar.inc')) {
                    require_once($MODULESDIR . DIRECTORY_SEPARATOR . $value.DIRECTORY_SEPARATOR.'pages/menubar.inc');
                }
            }
        }
    }

    foreach ($base_menus as $menu) {
        if (in_array($menu['function'], $DISABLEDMODULES)) {
            continue;
        }
        if ($menu['responsive']) {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles.$resp_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles.$resp_styles."\"";
            }
        } else {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles."\"";
            }
        }
        echo " href=\"index.php?Function=".$menu['function']."\" title=\"".$menu['title']."\">";
        echo "<i class=\"".$menu['i class']."\"></i>";
        echo "        ".$menu['text'];
        echo "</a>\n";
    }

    foreach ($admin_menus as $menu) {
        if (in_array($menu['function'], $DISABLEDMODULES)) {
            continue;
        }
        if ($menu['responsive']) {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles.$resp_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles.$resp_styles."\"";
            }
        } else {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles."\"";
            }
        }
        echo " href=\"index.php?Function=".$menu['function']."\" title=\"".$menu['title']."\">";
        echo "<i class=\"".$menu['i class']."\"></i>";
        echo "        ".$menu['text'];
        echo "</a>\n";
    }

?>

    <a class="w3-bar-item w3-block w3-center w3-button w3-hide-large"
      href="javascript:void(0)"
      onclick="myMenuBar()">
      <i class="fa fa-bars"></i>
    </a>
    <a class="w3-bar-item w3-button w3-red w3-right"
      href="index.php?Function=public" title="Log out and Restart">
      <i class="fa fa-sign-out"></i>
      Log Out
    </a>



    <!--Responsive menu for tables and moblie-->
    <div id="main_nav_list" class="w3-bar-block w3-hide w3-hide-large">

<?php
    foreach ($base_menus as $menu) {
        if (in_array($menu['function'], $DISABLEDMODULES)) {
            continue;
        }
        if ($menu['responsive']) {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles."\"";
            }
        } else {
            continue;
        }
        echo " href=\"index.php?Function=".$menu['function']."\" title=\"".$menu['title']."\">";
        echo "<i class=\"".$menu['i class']."\"></i>";
        echo "        ".$menu['text'];
        echo "</a>\n";
    }

    foreach ($admin_menus as $menu) {
        if (in_array($menu['function'], $DISABLEDMODULES)) {
            continue;
        }
        if ($menu['responsive']) {
            if ($_REQUEST['Function'] == $menu['function']) {
                echo "<a class=\"".$menu['selected style'].$base_styles."\"";
            } else {
                echo "<a class=\"".$menu['base style'].$base_styles."\"";
            }
        } else {
            continue;
        }
        echo " href=\"index.php?Function=".$menu['function']."\" title=\"".$menu['title']."\">";
        echo "<i class=\"".$menu['i class']."\"></i>";
        echo "        ".$menu['text'];
        echo "</a>\n";
    }
?>
    </div><!--close main_nav_list div-->
  </div><!--close main_nav div-->
</div>
<br />
<br />

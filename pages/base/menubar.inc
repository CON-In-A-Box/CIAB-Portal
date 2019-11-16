<script>
  function myMenuBar() {
    var x = document.getElementById("main_nav_list");
    if (x.className.indexOf("UI-show") == -1) {
      x.className += " UI-show";
    } else {
      x.className = x.className.replace(" UI-show", "");
    }
  }
</script>
<div class="UI-main-bar-div">
  <div id="main_nav" class="UI-main-bar">
  <img class="UI-main-bar-icon" src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' alt="Event Icon">

<?php

require_once $BASEDIR.'/kiosk/kiosk.inc';

if (!isset($base_styles)) {
    $base_styles = " UI-main-bar-item";
}
if (!isset($resp_styles)) {
    $resp_styles = " UI-main-responsive-bar-item";
}

$kiosk = get_kiosk();

if ($kiosk === null) {
    if (!isset($base_menus)) {
        $base_menus = array();
        $base_menus[] = array('base style' => '',
             'selected style' => 'event-color-primary',
             'function' => 'main',
             'title' => 'Main Page',
             'i class' => 'fas fa-home',
             'text' => 'Main',
             'responsive' => false);

        $base_menus[] = array('base style' => '',
             'selected style' => 'event-color-primary',
             'function' => 'profile',
             'title' => 'Update Profile Information',
             'i class' => 'fas fa-user',
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
            if ($kiosk === null && in_array($value, $DISABLEDMODULES)) {
                continue;
            }
            if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$value)) {
                if (is_file($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'pages/menubar.inc')) {
                    require_once($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'pages/menubar.inc');
                }
            }
        }
    }
} else {
    $base_menus = array();
    if (!isset($admin_menus)) {
        $admin_menus = array();
    }
    if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$kiosk)) {
        if (is_file($MODULESDIR.DIRECTORY_SEPARATOR.$kiosk.DIRECTORY_SEPARATOR.'pages/menubar.inc')) {
            require_once($MODULESDIR.DIRECTORY_SEPARATOR.$kiosk.DIRECTORY_SEPARATOR.'pages/menubar.inc');
        }
    }
}

foreach ($base_menus as $menu) {
    if ($kiosk === null && in_array($menu['function'], $DISABLEDMODULES)) {
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
    echo "<em class=\"".$menu['i class']."\"></em>";
    echo "        ".$menu['text'];
    echo "</a>\n";
}

$have_admin = false;
foreach ($admin_menus as $menu) {
    if ($kiosk === null && in_array($menu['function'], $DISABLEDMODULES)) {
        continue;
    }

    if (!$have_admin) {
        echo "<div class='UI-admin-menu-div'>";
        echo "<button class='UI-admin-menu ".$resp_styles."'><em  class='fas fa-key'></em> Administer</button>";
        echo "<div class='UI-admin-menu-end'>";
        $have_admin = true;
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
    echo "<em class=\"".$menu['i class']."\"></em>";
    echo "        ".$menu['text'];
    echo "</a>\n";
}

if ($have_admin) {
    echo "</div>";
    echo "</div>";
}

if ($kiosk === null) {
?>
    <a class="UI-main-bar-pancake"
      href="javascript:void(0)"
      onclick="myMenuBar()">
      <em class="fas fa-bars"></em>
    </a>
    <a class="UI-main-logout-bar-item"
      href="index.php?Function=public" title="Log out and Restart">
      <em class="fas fa-sign-out-alt"></em>
      Log Out
    </a>
<?php } ?>


    <!--Responsive menu for tables and moblie-->
    <div id="main_nav_list" class="UI-main-bar-pancake-div">

<?php
foreach ($base_menus as $menu) {
    if ($kiosk === null && in_array($menu['function'], $DISABLEDMODULES)) {
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
    echo "<em class=\"".$menu['i class']."\"></em>";
    echo "        ".$menu['text'];
    echo "</a>\n";
}

$have_admin = false;
foreach ($admin_menus as $menu) {
    if ($kiosk === null && in_array($menu['function'], $DISABLEDMODULES)) {
        continue;
    }

    if (!$have_admin) {
        echo "<div class='UI-admin-menu-div'>";
        echo "<button class='UI-admin-menu'><em class='fas fa-key'></em> Administer</button>";
        echo "<div class='UI-admin-menu-end'>";
        $have_admin = true;
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
    echo "<em class=\"".$menu['i class']."\"></em>";
    echo "        ".$menu['text'];
    echo "</a>\n";
}

if ($have_admin) {
    echo "</div>";
    echo "</div>";
}
?>
    </div><!--close main_nav_list div-->
  </div><!--close main_nav div-->
</div>
<br />
<br />

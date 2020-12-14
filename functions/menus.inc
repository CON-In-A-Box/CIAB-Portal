<?php

/*.
    require_module 'standard';
.*/

require_once $BASEDIR.'/kiosk/kiosk.inc';


function menu_disabled($menu)
{
    global $DISABLEDMODULES;
    static $kiosk = null;

    if ($kiosk === null) {
        $kiosk = in_kiosk();
    }
    return (!$kiosk && in_array($menu['function'], $DISABLEDMODULES));

}


function menu_element($menu, $base_styles, $responsive = null)
{
    $element = "";
    if ($menu['responsive']) {
        if ($_REQUEST['Function'] == $menu['function']) {
            $element .= "<a class=\"".$menu['selected style']." $base_styles $responsive\"";
        } else {
            $element .= "<a class=\"".$menu['base style']." $base_styles $responsive\"";
        }
    } else {
        if ($responsive) {
            if ($_REQUEST['Function'] == $menu['function']) {
                $element .= "<a class=\"".$menu['selected style']." $base_styles\"";
            } else {
                $element .= "<a class=\"".$menu['base style']." $base_styles\"";
            }
        } else {
            return "";
        }
    }

    $element .= " href=\"index.php?Function=".$menu['function']."\" title=\"".$menu['title']."\">";
    $element .= "<em class=\"".$menu['i class']."\"></em>&nbsp;";
    $element .= $menu['text'];
    $element .= "</a>\n";
    return $element;

}


function build_sub_menu($menu, $base_styles, $resp_styles)
{
    $menus_out = build_menus($menu['function'], $base_styles, $resp_styles);
    if ($resp_styles) {
        $menu_class = $menu['base style'];
        $div_class = '';
        if (strlen($menus_out) == 0) {
            $div_class = 'UI-hide';
        }
        $output = "<div class='UI-menu-div $div_class'>";
        $output .= "<button class='$menu_class ".$resp_styles."'";
        $output .= "title='".$menu['title']."'>";
        $output .= "<em class='".$menu['i class']."'></em> ";
        $output .= $menu['text']."</button>";
        $output .= "<div class='UI-menu-end' id = '".$menu['text']."'>";
        $output .= $menus_out;
        $output .= "</div>";
        $output .= "</div>";
        return $output;
    } else {
        return $menus_out;
    }
    return "";

}


function build_menus($base, $base_styles, $resp_styles = null)
{
    $element = "";
    foreach ($base as $menu) {
        if (empty($menu) || menu_disabled($menu)) {
            continue;
        }
        if (is_array($menu['function'])) {
            $element .= build_sub_menu($menu, $base_styles, $resp_styles);
        } else {
            $element .= menu_element($menu, $base_styles, $resp_styles);
        }
    }
    return $element;

}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo $CONSITENAME; ?> - Bridge Log</title>
<?php
if (is_file($SITESUPPORTDIR . "/" . $_REQUEST['Function'] . '_style.css')) {
  echo "<link href=\"sitesupport/" . $_REQUEST['Function'] . '_style.css' . "\" rel=\"stylesheet\" type=\"text/css\" />";
} else {
  echo "<link href=\"sitesupport/main_style.css\" rel=\"stylesheet\" type=\"text/css\" />";
}
?>
<!--[If IE]>
<style type ="text/css">
/* css fixes for IE */
.bodyclass #navbar_left { padding-top: 30px; }
.bodyclass #main_content { zoom: 1; padding-top: 15px; }
</style>
<![endif]-->
<link href="images/favicon.ico" rel="icon" type="image/vnd.microsoft.icon">
<link href="images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
<?php
?>
<script src="sitesupport/sidebar.js"></script>
<script src="sitesupport/barcode/quagga.min.js"></script>
<script src="sitesupport/barcode/barcode.js"></script>
<script src="sitesupport/confirmbox.js"></script>
<script src="sitesupport/lookupuser.js"></script>
<script src="modules/registration/sitesupport/registration.js"></script>
<link rel="stylesheet" type="text/css" href="sitesupport/barcode/barcode.css" />
<?php
require_once $BASEDIR.'/console/console.inc';
if (function_exists('get_console') && get_console()) {
    console_head();
}

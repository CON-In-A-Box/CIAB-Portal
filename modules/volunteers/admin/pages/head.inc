<?php
?>
<script src="sitesupport/sidebar.js"></script>
<script src="sitesupport/barcode/quagga.min.js"></script>
<script src="sitesupport/barcode/barcode.js"></script>
<script src="sitesupport/alertbox.js"></script>
<script src="sitesupport/common.js"></script>
<script src="modules/volunteers/sitesupport/volunteers.js"></script>
<script src="sitesupport/lookupuser.js"></script>
<script src="sitesupport/confirmbox.js"></script>
<link rel="stylesheet" type="text/css" href="sitesupport/barcode/barcode.css" />
<style>
.hiddenrow {
    display:none;
};

</style>
<?php
require_once $BASEDIR.'/kiosk/kiosk.inc';
kiosk_head();

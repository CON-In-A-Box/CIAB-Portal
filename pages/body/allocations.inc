<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content"  class="w3-cell w3-cell-top w3-mobile">
<?php
echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>\n";

require_once($FUNCTIONDIR . "/divisional.inc");
DumpConComList();
?>
</div><!--close main_content div-->

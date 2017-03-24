<?php
if(!empty($_REQUEST['DeepLink'])) { ?>
  <header class="w3-cell-row w3-green w3-mobile">
    <?php require($PAGESDIR . '/base/logo.inc'); ?>
    <div class="w3-container w3-cell w3-mobile">
      <h2><?php echo $CONHOST . " Hotel Allocations Portal"; ?></h2>
    </div><!--close header text div-->
  </header><!--close header div-->
<?php
} else {
  require($PAGESDIR . '/base/leftnav.inc');
?>
  <div id="main_content"  class="w3-cell w3-cell-top w3-mobile">
<?php
  echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>\n";
}



?>
</div><!--close main_content div-->

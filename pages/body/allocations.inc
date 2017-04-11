<?php
require_once($FUNCTIONDIR . '/allocations.inc');
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
}

$counts = totalAvailableRooms('DT');
?>
    <div class='w3-responsive'>
      <table class='w3-table-all w3-hoverable'>
        <tr class='w3-blue'>
          <th class='w3-center' colspan=6>
            Doubletree by Hilton
          </th>
        </tr>
        <tr class='w3=blue'>
          <th class='w3-white'></th>
          <th>Total</th>
          <th>King</th>
          <th>Double</th>
          <th>Suite</th>
          <th>HC</th>
        </tr>
        <tr>
<?php
echo '<td>Total in Hotel</td>' . "\n";
echo '<td>' . $counts['Total'] . "</td>\n";
echo '<td>' . $counts['King'] . "</td>\n";
echo '<td>' . $counts['Double'] . "</td>\n";
echo '<td>' . $counts['Suite'] . "</td>\n";
echo '<td>' . $counts['HC'] . "</td>\n";
?>
        </tr>
      </table>
    </div>
  </div><!--close main_content div-->

<?php
echo "<div class=\"log_entry\">\n";

while ($row=mysql_fetch_array($results)) {
  echo "<div class=\"entry_chunk\">\n";
  echo $row[0] . "\n";
  echo "</div><!--close entry_chunk div-->\n";
}
echo "</div><!--close log_entry div-->\n";
?>

<?php if (isset($_SESSION['IS_ADMIN'])) { ?>
<a class="w3-bar-item w3-button<?php
if ($_REQUEST['Function'] == 'admin') {
    echo " w3-green";
}
    ?>" href="index.php?Function=admin" title="Oh the adminitration">
  <i class="fa fa-key"></i>
  Administration
</a>
<?php } ?>

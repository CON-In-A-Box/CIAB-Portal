<?php if (isset($_SESSION['IS_ADMIN'])) { ?>
  <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
    if ($_REQUEST['Function'] == 'admin') {
        echo " w3-green";
    } else {
        echo " w3-yellow";
    }
    ?>" href="index.php?Function=admin" title="Oh the adminitration">
    <i class="fa fa-key"></i>
    Administration
  </a>
<?php } ?>

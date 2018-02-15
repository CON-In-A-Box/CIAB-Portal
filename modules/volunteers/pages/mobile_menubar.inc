<?php if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
<a class="w3-bar-item w3-button<?php
if ($_REQUEST['Function'] == 'volunteers/admin') {
    echo " w3-green";
}
    ?>" href="index.php?Function=volunteers/admin" title="Volunteers">
  <i class="fa fa-users"></i>
  Volunteers
</a>
<?php } ?>

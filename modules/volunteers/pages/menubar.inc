<?php if (isset($_SESSION['customFields']['currConComPos'])) { ?>
    <?php if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
  <a class="w3-bar-item w3-button<?php
    if ($_REQUEST['Function'] == 'volunteers/enter') {
        echo " w3-green";
    }
    ?>" href="index.php?Function=volunteers/enter" title="Track Volunteer's hours">
    <i class="fa fa-clock-o"></i>
    Enter Hours
  </a>
<?php }
} ?>

<?php if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
  <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
    if ($_REQUEST['Function'] == 'volunteers/admin') {
        echo " w3-green";
    } elseif ($_COOKIE["CIAB_VOLUNTEERADMIN"]) {
        echo " w3-red";
    } else {
        echo " w3-yellow";
    }
    ?>" href="index.php?Function=volunteers/admin" title="Volunteers">
    <i class="fa fa-users"></i>
    Volunteers
  </a>
<?php } ?>

<script>
  function myMenuBar() {
    var x = document.getElementById("main_nav_list");
    if (x.className.indexOf("w3-show") == -1) {
      x.className += " w3-show";
    } else { 
      x.className = x.className.replace(" w3-show", "");
    }
  }
</script>
<div class="w3-top">
  <div id="main_nav" class="w3-bar w3-dark-grey">
    <a class="w3-bar-item w3-button<?php
      if ($_REQUEST['Function'] == 'main') echo " w3-green";
      ?>" href="index.php?Function=main" title="Main Page">
      <i class="fa fa-home"></i>
      Main
    </a>
    <?php if ( isset( $_SESSION['customFields']['currConComPos'])) { ?>
      <?php if(isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
      <a class="w3-bar-item w3-button<?php
        if ($_REQUEST['Function'] == 'volhours') echo " w3-green";
        ?>" href="index.php?Function=volhours" title="Track Volunteer's hours">
        <i class="fa fa-clock-o"></i>
        Enter Hours
      </a>
      <?php } ?>
      <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
        if ($_REQUEST['Function'] == 'list') echo " w3-green";
        ?>" href="index.php?Function=list" title="The Current ConCom List List">
        <i class="fa fa-phone"></i>
        ConCom List
      </a>
    <?php } ?>
    <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
      if ($_REQUEST['Function'] == 'profile') echo " w3-green";
      ?>" href="index.php?Function=profile" title="Update Profile Informaiton In Neon">
      <i class="fa fa-user"></i>
      Profile
    </a>
    <?php if(isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
      <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
        if ($_REQUEST['Function'] == 'volunteers') { echo " w3-green"; } else { echo " w3-yellow"; }
        ?>" href="index.php?Function=volunteers" title="Volunteers">
        <i class="fa fa-users"></i>
        Volunteers
      </a>
    <?php } ?>
    <?php if(isset($_SESSION['IS_ALLOCATIONS']) || isset($_SESSION['IS_ADMIN'])) { ?>
      <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
        if ($_REQUEST['Function'] == 'allocations') { echo " w3-green"; } else { echo " w3-yellow"; }
        ?>" href="index.php?Function=allocations" title="Hotel Allocations">
        <i class="fa fa-bed"></i>
        Hotel Allocations
      </a>
    <?php } ?>
    <?php if(isset($_SESSION['IS_ADMIN'])) { ?>
      <a class="w3-bar-item w3-button w3-hide-small w3-hide-medium<?php
        if ($_REQUEST['Function'] == 'admin') { echo " w3-green"; } else { echo " w3-yellow"; }
        ?>" href="index.php?Function=admin" title="Oh the adminitration">
        <i class="fa fa-key"></i>
        Administration
      </a>
    <?php } ?>
    <a class="w3-bar-item w3-block w3-center w3-button w3-hide-large"
      href="javascript:void(0)"
      onclick="myMenuBar()">
      <i class="fa fa-bars"></i>
    </a>
    <a class="w3-bar-item w3-button w3-red w3-right"
      href="index.php?Function=public" title="Log out and Restart">
      <i class="fa fa-sign-out"></i>
      Log Out
    </a>

    <!--Responsive menu for tables and moblie-->
    <div id="main_nav_list" class="w3-bar-block w3-hide w3-hide-large">
      <?php if ( isset( $_SESSION['customFields']['currConComPos'])) { ?>
        <a class="w3-bar-item w3-button<?php
          if ($_REQUEST['Function'] == 'list') echo " w3-green";
          ?>" href="index.php?Function=list" title="The Current ConCom List List">
          <i class="fa fa-phone"></i>
          ConCom List
        </a>
      <?php } ?>
      <a class="w3-bar-item w3-button<?php
        if ($_REQUEST['Function'] == 'profile') echo " w3-green";
        ?>" href="index.php?Function=profile" title="Update Profile Informaiton In Neon">
        <i class="fa fa-user"></i>
        Profile
      </a>
      <?php if(isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) { ?>
        <a class="w3-bar-item w3-button<?php
          if ($_REQUEST['Function'] == 'volunteers') echo " w3-green";
          ?>" href="index.php?Function=volunteers" title="Volunteers">
          <i class="fa fa-users"></i>
          Volunteers
        </a>
      <?php } ?>
      <?php if(isset($_SESSION['IS_ALLOCATIONS']) || isset($_SESSION['IS_ADMIN'])) { ?>
        <a class="w3-bar-item w3-button<?php
          if ($_REQUEST['Function'] == 'allocations') echo " w3-green";
          ?>" href="index.php?Function=allocations" title="Hotel Allocations">
          <i class="fa fa-bed"></i>
          Hotel Allocations
        </a>
      <?php } ?>
      <?php if(isset($_SESSION['IS_ADMIN'])) { ?>
        <a class="w3-bar-item w3-button<?php
          if ($_REQUEST['Function'] == 'admin') echo " w3-green";
          ?>" href="index.php?Function=admin" title="Oh the adminitration">
          <i class="fa fa-key"></i>
          Administration
        </a>
      <?php } ?>
    </div><!--close main_nav_list div-->
  </div><!--close main_nav div-->
</div>
<br />
<br />

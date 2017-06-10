<?php
  if (empty( $_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
  }

  if (!empty($_POST)) {
    // Retrieve and sanitize POST data
    $arguments = [
      'volunteer'          => FILTER_SANITIZE_SPECIAL_CHARS,
      'hours'              => FILTER_SANITIZE_NUMBER_FLOAT,
      'datetime'           => FILTER_SANITIZE_SPECIAL_CHARS,
      'modifier'           => FILTER_SANITIZE_NUMBER_FLOAT,
      'department'         => FILTER_SANITIZE_SPECIAL_CHARS,
      'enterer'            => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    if (!empty($_POST)) {
      $updateData = filter_input_array( INPUT_POST, $arguments );
    } else {
      $updateData = filter_input_array( INPUT_GET, $arguments );
    }

    

  }
?>


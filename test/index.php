<?php
require_once('.ht_neon.php');
require_once('neon.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>NeonCRM Test</title>
</head>

<body>

<?
echo "<p>hi</p>\n";
$neon = new Neon();
$keys = array(
  'orgId' => $neonId,
  'apiKey' => $neonKey,
  );
$neon->login($keys);

$request = array(
  'method' => 'common/listCustomFields',
  'parameters' => array(
    'searchCriteria.component' => 'Event',
    ),
  );
$result = $neon->go($request);
echo "<pre>\n";
var_dump($result);
echo "</pre>\n";
echo "<p>post.</p>\n";
?>
</body>
</html>

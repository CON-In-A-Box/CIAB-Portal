<?php
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

$request = [
'method' => 'event/retrieveEventAttendees',
'parameters' => [
'eventId' => 132,
'page.currentPage' => 2,
'page.pageSize' => 20,
  ],
];
$result = $neon->go($request);

echo "<pre>\n";
var_dump($result);
echo "</pre>\n";
echo "<p>post.</p>\n";
?>
</body>
</html>

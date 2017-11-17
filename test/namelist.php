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

$search = array(
  'method' => 'account/listAccounts',
  'criteria' => array(
    array( 'Last Name', 'EQUAL', 'Keeley'),
  ),
  'columns' => array(
    'standardFields' => array('Account ID', 'First Name', 'Last Name'),
  ),
  'page' => array(
    'currentPage' => 1,
    'pageSize' => 20,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
  ),
);
$result = $neon->search($search);

echo "<pre>\n";
var_dump($result);
echo "</pre>\n";
echo "<p>post.</p>\n";
?>
</body>
</html>

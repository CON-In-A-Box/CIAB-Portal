<?php
require_once('functions/neon.inc');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>NeonCRM Test</title>
</head>

<body>

<?
echo "<p>hi</p>\n";

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

$search = array(
  'method' => 'account/listAccounts',
  'criteria' => array(
    array( 'First Name', 'EQUAL', 'Thomas'),
    array( 'Last Name', 'EQUAL', 'Keeley'),
  ),
  'columns' => array(
    'standardFields' => array('Account Id', 'First Name', 'Last Name'),
    'customFields' => array(655, 361),
  ),
  'page' => array(
    'currentPage' => 1,
    'pageSize' => 20,
    'sortColumn' => 'Account Id',
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


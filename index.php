<?php
$version = "1.0.0";

include_once('Essential/functions/functions.php');

if (urlquery('mode') == 'realtime')
  include('indexpages/realtime.php');
else
  include('indexpages/routeselection.php');
?>


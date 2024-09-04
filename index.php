<?php
$version = "1.2.7";
$placeads = false;

include_once('Essential/functions/functions.php');

if (urlquery('mode') == 'realtime')
  include('indexpages/realtime.php');
else
  include('indexpages/routeselection.php');
?>


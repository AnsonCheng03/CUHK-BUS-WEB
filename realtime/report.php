<?php

date_default_timezone_set("Asia/Hong_Kong");
session_start();

include_once('../Essential/functions/functions.php');
$lang = $_POST["lang"];
foreach (array_slice(csv_to_array("../Data/Translate"), 1) as $row) {
    if ($row[0] !== "" && substr($row[0], 0, 2) !== "//") {
        $translation[$row[0]] = array($row[2], $row[3]);
        $translation[$row[0]][] = $row[1];
    }
}


if(!isset($_POST['CSRF']) || $_POST['CSRF'] != $_SESSION['_token'] ) die('驗證錯誤！ 請再試一次～ \nToken Error! Please try again~');


if(isset($_SESSION['Lastpost'])) 
    if($_SESSION['Lastpost'] >= (new DateTime())->modify("-1 minutes")->format('YmdHis'))
        die($translation['repeat_submit'][$lang]);

// Create connection
$conn = new mysqli("localhost", "u344988661_cubus", "*rV0J2J5", "u344988661_cubus");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// prepare and bind
$stmt = $conn->prepare("INSERT INTO `report` (`Serial`, `Time`, `BusNum`, `StopAttr`) VALUES (NULL, ?, ?, ?);");
$stmt->bind_param("sss", $Time, $BusNum, $Stop);

// set parameters and execute
$Time = (new DateTime())->format('Y-m-d H:i:s');
$BusNum = $_POST['linename'];
$Stop = $_POST['stop'];
$stmt->execute();

echo $translation['submit_success'][$lang];

$stmt->close();
$conn->close();

$_SESSION['Lastpost'] = (new DateTime())->format('YmdHis');
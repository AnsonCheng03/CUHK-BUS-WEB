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

foreach (array_slice(csv_to_array("../Data/GPS"), 1) as $row) {
    $GPS[$row[0]]["Lat"] = $row[1];
    $GPS[$row[0]]["Lng"] = $row[2];
}

function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);
    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
    $angle = atan2(sqrt($a), $b);
    return $angle * $earthRadius;
}

if(vincentyGreatCircleDistance($GPS[$_POST['stop']]["Lat"],$GPS[$_POST['stop']]["Lng"] ,$_POST['positionlat'] ,$_POST['positionlng'] ) > 300)
    die($translation['distancetoolong_warning'][$lang]);

if (!isset($_POST['CSRF']) || $_POST['CSRF'] != $_SESSION['_token']) 
    die($translation['token_error'][$lang]);


if (isset($_SESSION['Lastpost']))
    if ($_SESSION['Lastpost'] >= (new DateTime())->modify("-1 minutes")->format('YmdHis'))
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

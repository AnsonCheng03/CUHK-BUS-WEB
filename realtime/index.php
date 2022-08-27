<?php


date_default_timezone_set("Asia/Hong_Kong");
include('../Essential/functions/functions.php');
$lang = $_POST['lang'];

foreach (csv_to_array("../Data/Route") as $busno) {
    $bus[$busno[0]]["schedule"] = array($busno[1], $busno[2], $busno[3], $busno[4], $busno[5]);
    foreach (array_filter(array_slice($busno, 6)) as $key => $value) {
        $statnm = strstr($value, '|', true) ?: $value;
        $attr = substr(strstr($value, '|', false), 1) ?: "NULL";
        $time = substr(strstr($attr, '|', false), 1) ?: "0";
        $attr = strstr($attr, '|', true) ?: "NULL";
        $bus[$busno[0]]["stations"]["name"][] = $statnm;
        $bus[$busno[0]]["stations"]["attr"][] = $attr;
        $bus[$busno[0]]["stations"]["time"][] = floatval($time);
    }
}

//Website Translation
foreach (array_slice(csv_to_array("../Data/Translate"), 1) as $row) {
    if ($row[0] !== "" && substr($row[0], 0, 2) !== "//") {
        $translation[$row[0]] = array($row[2], $row[3]);
        $translation[$row[0]][] = $row[1];
    }
}

$busschedule = json_decode(file_get_contents('../Data/timetable.json'), true);

$conn = new mysqli("localhost", "u344988661_cubus", "*rV0J2J5", "u344988661_cubus");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$stmt = $conn->prepare("INSERT INTO `logs` (`Time`, `Webpage`, `Dest`, `Lang`) 
VALUES (?, 'realtime', ?, ?);");
$stmt->bind_param("sss", $Time, $_POST['Dest'], $lang);
$Time = (new DateTime())->format('Y-m-d H:i:s');
$stmt->execute();
$stmt->close();
$conn->close();

//Bus Status

$busservices = json_decode(file_get_contents('../Data/Status.json'), true);
$currentbusservices = end($busservices);
$thirtyminbusservice = array_pop(array_slice($busservices, -30, 1));
if (isset($currentbusservices['ERROR'])) {
    $fetcherror = true;
    alert("alert", $translation["fetch-error"][$lang]);
} else {
    foreach ($currentbusservices as $busnumber => $busstatus) {
        $bus[$busnumber]["stats"]["status"] = $busstatus;
        $bus[$busnumber]["stats"]["prevstatus"] = $thirtyminbusservice[$busnumber];
        if (isset($bus[$busnumber . "#"])) {
            $bus[$busnumber . "#"]["stats"]["status"] = $busstatus;
            $bus[$busnumber . "#"]["stats"]["prevstatus"] = $thirtyminbusservice[$busnumber];
        }
    }
}

if (isset($fetcherror)) {
    foreach ($bus as $index => $busarr) {
        //Base on time
        $currenttime = (new DateTime())->getTimestamp();
        $starttime = (DateTime::createFromFormat("H:i", $busarr["schedule"][0]))->getTimestamp();
        $endtime = (DateTime::createFromFormat("H:i", $busarr["schedule"][1]))->getTimestamp();
        if ($currenttime < $starttime || $currenttime > $endtime)
            unset($bus[$index]);

        //Base on weekday or day
        if (strpos($busarr["schedule"][4], "WK-" . (new DateTime())->format('D')) === false)
            unset($bus[$index]);
    }
} else {
    foreach ($bus as $index => $busarr)
        if (($busarr["stats"]["status"] == "no" && $busarr["stats"]["prevstatus"] != "normal") || ($busarr["stats"]["status"] == "suspended" && $busarr["stats"]["prevstatus"] != "normal"))
            unset($bus[$index]);
}
$bus = array_filter($bus);


$outputschedule = array_filter($busschedule, function ($key) {
    return explode("|", $key)[0] == $_POST['Dest'];
}, ARRAY_FILTER_USE_KEY);

session_start();
$conn = new mysqli("localhost", "u344988661_cubus", "*rV0J2J5", "u344988661_cubus");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(32));

$conn->query("SET SESSION time_zone = '+8:00'");
$stmt = $conn->prepare("SELECT
DATE_FORMAT(`Time`,'%H'), FLOOR(DATE_FORMAT(`Time`,'%i')/2)*2 , COUNT(*) 
FROM `report` 
WHERE (`Time` >= (now() - interval 30 minute)) AND `BusNum` = ? AND `StopAttr` = ? 
GROUP BY CONCAT( DATE_FORMAT(`Time`,'%m-%d-%Y %H:'), FLOOR(DATE_FORMAT(`Time`,'%i')/2)*2)");

$stmt->bind_param("ss", $busnum, $Stop);

 



$countoutput = 0;
foreach ($outputschedule as $stationname => $schedule) {
    ksort($schedule);
    foreach ($schedule as $busno => $timetable) {
        if (isset($bus[$busno]) && $timetable) {
            echo "
                <div class='bussect'>
                    <div class='busname'>" . $busno .
                "<button data='" . $busno . "' lang='" . $lang . "' tk='" . $_SESSION['_token'] . "' stop='" .  $stationname . "' onclick='realtimesubmit(this);'>" . $translation['bus-arrive-btn'][$lang] . "</button>" .
                "</div>";

            $busnum = $busno;
            $Stop = $stationname;
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                echo "
                    <div class=\"userreport\">
                        <div class=\"businfo\">[ " . $row[2] . " ] " . $translation['schoolbus_arrival'][$lang] . "</div>
                        <div class=\"arrtime\"> ~ " . $row[0] . ":" . sprintf('%02d', $row[1]) . "</div>
                    </div>
                ";
            }

            $outputcountcount = 0;
            $nowtime = (new DateTime())->format('H:i:s');
            $currtime = (new DateTime())->modify("-30 minutes")->format('H:i:s');
            sort($timetable);
            foreach ($timetable as $time) {
                if ($time >= $currtime) {
                    echo "<div class='bustype" . ($time <= $nowtime ? ' arrived' : "") . "'>";
                    echo "<div class='businfo'>" .
                        (explode("|", $stationname)[1] ? $translation[explode("|", $stationname)[1]][$lang] : $translation["mode-realtime"][$lang]) .
                        "</div>";
                    echo "<div class='arrtime'> ~ " . substr($time, 0, -3) . "</div>";
                    echo "</div>";
                    $outputcountcount++;
                }
                if ($outputcountcount >= 5) break;
            }
            $countoutput++;
            echo "
                </div>
            ";
        }
    }
}
if ($countoutput == 0) {
    echo '<div class=\'bussect\'><div class=\'busname\'>' . $translation["No-bus-time"][$lang] . '</div></div>';
}

$stmt->close();
$conn->close();

<?php

date_default_timezone_set("Asia/Hong_Kong");
include('../Essential/functions/functions.php');
include_once('../Essential/functions/loadenv.php');

$lang = $_POST['lang'] ?? 'en';
$dest = $_POST['Dest'] ?? '';

$initdataitems = [
    "Route" => true,
    "Translate" => true,
];
include('../Essential/functions/initdatas.php');

// Load bus schedule and status
$busschedule = json_decode(file_get_contents('../Data/timetable.json'), true);
$busservices = json_decode(file_get_contents('../Data/Status.json'), true);

// Log the request if not a loop
if ($_POST['loop'] !== 'loop') {
    logRequest($dest, $lang);
}

// Process bus status
$currentbusservices = end($busservices);
$thirtyminbusservice = array_slice($busservices, -30, 1)[0] ?? [];

if (isset($currentbusservices['ERROR'])) {
    alert("alert", $translation["fetch-error"][$lang]);
    $bus = filterBusesBySchedule($bus);
} else {
    $bus = processBusStatus($currentbusservices, $thirtyminbusservice, $bus);
}

$outputschedule = array_filter($busschedule, fn($key) => explode("|", $key)[0] == $dest, ARRAY_FILTER_USE_KEY);

// Generate CSRF token
$_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(32));

// Process and display bus information
$allBuses = processAndSortBuses($outputschedule, $bus, $lang, $translation);
displayBuses($allBuses, $lang, $_SESSION['_token'] ?? 'null', $translation, $dest);

function logRequest($dest, $lang)
{
    $conn = connectToDatabase();
    $stmt = $conn->prepare("INSERT INTO `logs` (`Time`, `Webpage`, `Dest`, `Lang`) VALUES (NOW(), 'realtime', ?, ?)");
    $stmt->bind_param("ss", $dest, $lang);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function filterBusesBySchedule($bus)
{
    $currenttime = time();
    return array_filter($bus, function ($busarr) use ($currenttime) {
        $starttime = strtotime($busarr["schedule"][0]);
        $endtime = strtotime($busarr["schedule"][1]);
        $weekday = "WK-" . date('D');
        return $currenttime >= $starttime && $currenttime <= $endtime && strpos($busarr["schedule"][4], $weekday) !== false;
    });
}

function processBusStatus($currentbusservices, $thirtyminbusservice, $bus)
{
    foreach ($currentbusservices as $busnumber => $busstatus) {
        $bus[$busnumber]["stats"] = [
            "status" => $busstatus,
            "prevstatus" => $thirtyminbusservice[$busnumber] ?? null
        ];
        if (isset($bus[$busnumber . "#"])) {
            $bus[$busnumber . "#"]["stats"] = $bus[$busnumber]["stats"];
        }
    }
    return array_filter(
        $bus,
        fn($busarr) =>
        $busarr["stats"]["status"] != "no" || $busarr["stats"]["prevstatus"] == "normal"
    );
}

function processAndSortBuses($outputschedule, $bus, $lang, $translation)
{
    $allBuses = [];
    $conn = connectToDatabase();
    $stmt = prepareStatement($conn);

    $nowtime = date('H:i:s');
    $currtime = date('H:i:s', strtotime("-30 minutes"));

    foreach ($outputschedule as $stationname => $schedule) {
        foreach ($schedule as $busno => $timetable) {
            if (isset($bus[$busno]) && $timetable) {
                $allBuses = array_merge($allBuses, getUserReportedTimes($stmt, $busno, $stationname, $lang, $translation));
                $allBuses = array_merge($allBuses, getScheduledTimes($timetable, $busno, $stationname, $currtime, $nowtime, $lang, $translation));
            }
        }
    }

    $stmt->close();
    $conn->close();

    usort($allBuses, fn($a, $b) => strtotime($a['time']) - strtotime($b['time']));
    return $allBuses;
}

function connectToDatabase()
{
    $conn = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->query("SET SESSION time_zone = '+8:00'");
    return $conn;
}

function prepareStatement($conn)
{
    return $conn->prepare("SELECT
        DATE_FORMAT(`Time`,'%H'), FLOOR(DATE_FORMAT(`Time`,'%i')/2)*2 , COUNT(*) 
        FROM `report` 
        WHERE (`Time` >= (now() - interval 30 minute)) AND `BusNum` = ? AND `StopAttr` = ? 
        GROUP BY CONCAT( DATE_FORMAT(`Time`,'%m-%d-%Y %H:'), FLOOR(DATE_FORMAT(`Time`,'%i')/2)*2)");
}

function getUserReportedTimes($stmt, $busno, $stationname, $lang, $translation)
{
    $stmt->bind_param("ss", $busno, $stationname);
    $stmt->execute();
    $result = $stmt->get_result();
    $userReportedTimes = [];
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        $userReportedTimes[] = [
            'busno' => $busno,
            'direction' => $translation['schoolbus_arrival'][$lang],
            'time' => $row[0] . ":" . sprintf('%02d', $row[1]),
            'isUserReport' => true,
            'reportTime' => $row[2]
        ];
    }
    return $userReportedTimes;
}

function getScheduledTimes($timetable, $busno, $stationname, $currtime, $nowtime, $lang, $translation)
{
    $scheduledTimes = [];
    foreach ($timetable as $time) {
        if ($time >= $currtime) {
            $scheduledTimes[] = [
                'busno' => $busno,
                'direction' => (explode("|", $stationname)[1] ? $translation[explode("|", $stationname)[1]][$lang] : $translation["mode-realtime"][$lang]),
                'time' => substr($time, 0, -3),
                'isUserReport' => false,
                'arrived' => ($time <= $nowtime)
            ];
        }
    }
    return $scheduledTimes;
}

function displayBuses($allBuses, $lang, $token, $translation, $dest)
{
    $countoutput = 0;
    foreach ($allBuses as $bus) {
        echo "<div class='bussect'>";
        echo "<div class='busname'>" . $bus['busno'] .
            "<button data='" . $bus['busno'] . "' lang='" . $lang . "' tk='" . $token .
            "' stop='" . $dest . "' onclick='realtimesubmit(this);'>" . $translation['bus-arrive-btn'][$lang] . "</button>" .
            "</div>";

        if ($bus['isUserReport']) {
            echo "<div class=\"userreport\">";
            echo "<div class=\"businfo\">[ " . $bus['reportTime'] . " ] " . $bus['direction'] . "</div>";
        } else {
            echo "<div class='" . ($bus['arrived'] ? 'bustype arrived' : 'bustype') . "'>";
            echo "<div class='businfo'>" . $bus['direction'] . "</div>";
        }

        echo "<div class='arrtime'> ~ " . $bus['time'] . "</div>";
        echo "</div>";
        echo "</div>";

        $countoutput++;
    }

    if ($countoutput == 0) {
        echo '<div class=\'bussect\'><div class=\'busname\'>' . $translation["No-bus-time"][$lang] . '</div></div>';
    }
}
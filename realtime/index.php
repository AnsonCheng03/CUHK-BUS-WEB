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

$busschedule = json_decode(file_get_contents('../Data/timetable.json'), true);
$busservices = json_decode(file_get_contents('../Data/Status.json'), true);

if ($_POST['loop'] !== 'loop') {
    logRequest($dest, $lang);
}

$currentbusservices = end($busservices);
$thirtyminbusservice = array_slice($busservices, -60, 1)[0] ?? [];

if (isset($currentbusservices['ERROR'])) {
    alert("alert", $translation["fetch-error"][$lang]);
    $bus = filterBusesBySchedule($bus);
} else {
    $bus = filterBusesBySchedule($bus);
    $bus = processBusStatus($currentbusservices, $thirtyminbusservice, $bus);
}

$outputschedule = array_filter($busschedule, fn($key) => explode("|", $key)[0] == $dest, ARRAY_FILTER_USE_KEY);

$_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(32));

$allBuses = processAndSortBuses($outputschedule, $bus, $lang, $translation);

displayBuses($allBuses, $lang, $translation);

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
    return array_filter($bus, function ($busarr) {
        $weekday = "WK-" . date('D');
        return strpos($busarr["schedule"][4], $weekday) !== false;
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
    // Instead of filtering out buses, we'll keep them all and add a warning flag
    foreach ($bus as $busnumber => &$busarr) {
        if ($busarr["stats"]["status"] == "no" && $busarr["stats"]["prevstatus"] != "normal") {
            $busarr["warning"] = "No-bus-available";
        } else if ($busarr["stats"]["status"] != "normal") {
            $busarr["warning"] = "Bus-status-unusual";
        }
    }
    return $bus;
}

function processAndSortBuses($outputschedule, $bus, $lang, $translation)
{
    $allBuses = [];
    $nowtime = date('H:i:s');
    $currtime = date('H:i:s', strtotime("-5 minutes"));

    foreach ($outputschedule as $stationname => $schedule) {
        foreach ($schedule as $busno => $timetable) {
            if (isset($bus[$busno]) && $timetable) {
                $warning = $bus[$busno]['warning'] ?? false;
                $nextStation = getNextStation($bus[$busno]['stations'] ?? [], $stationname);
                $allBuses = array_merge($allBuses, getScheduledTimes($timetable, $busno, $stationname, $currtime, $nowtime, $lang, $translation, $warning, $nextStation));
            }
        }
    }

    usort($allBuses, fn($a, $b) => strtotime($a['time']) - strtotime($b['time']));
    return $allBuses;
}

function getNextStation($stations, $currentStation)
{
    [$currentStationName, $currentStationAttr] = explode('|', $currentStation) + [1 => null];

    $foundIndex = -1;
    foreach ($stations['name'] as $index => $name) {
        if (
            $name === $currentStationName &&
            ($currentStationAttr === null || $stations['attr'][$index] === $currentStationAttr)
        ) {
            $foundIndex = $index;
            break;
        }
    }

    if ($foundIndex === -1 || $foundIndex === count($stations['name']) - 1) {
        return null;
    }

    return $stations['name'][$foundIndex + 1];
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


function getScheduledTimes($timetable, $busno, $stationname, $currtime, $nowtime, $lang, $translation, $warning, $nextStation)
{
    $scheduledTimes = [];
    foreach ($timetable as $time) {
        if ($time >= $currtime) {
            $scheduledTimes[] = [
                'busno' => $busno,
                'direction' => (explode("|", $stationname)[1] ? $translation[explode("|", $stationname)[1]][$lang] : $translation["mode-realtime"][$lang]),
                'time' => substr($time, 0, -3),
                'arrived' => ($time <= $nowtime),
                'warning' => $warning,
                'nextStation' => $nextStation
            ];
        }
    }
    return $scheduledTimes;
}

function displayBuses($allBuses, $lang, $translation)
{
    echo "<div class='bus-grid'>";
    $countoutput = 0;

    foreach ($allBuses as $bus) {
        if ($countoutput >= 10)
            break;

        $busName = $bus['busno'];
        $direction = $bus['direction'] !== $translation["mode-realtime"][$lang] ? "<br>" . $bus['direction'] : "";
        $arrivalTime = $bus['time'];

        echo "<div class='bus-row" . ($bus['arrived'] ? ' arrived' : '') . "' onclick='window.open(\"/pages/blogs/routes/$busName/\"); return false;'>";
        echo "<div class='bus-info'><span class='bus-name'>" . $busName . "</span><span class='direction'>$direction</span></div>";
        echo "<div class='next-station-display'>";
        echo "<p class='next-station-text'>" . $translation["next-station"][$lang] . "</p>";
        if ($bus['nextStation']) {
            echo "<p class='next-station'>" . $translation[$bus['nextStation']][$lang] . "</p>";
        }
        if ($bus['warning']) {
            echo "<span></span><span class='warning'>" . htmlspecialchars($translation[$bus['warning']][$lang]) . "</span>";
        }
        echo "</div>";
        echo "<div class='arrival-time'>" . htmlspecialchars($arrivalTime) . "</div>";
        echo "</div>";

        if (!$bus['arrived'])
            $countoutput++;
    }
    echo "</div>";

    if ($countoutput == 0) {
        echo '<div class="no-bus"><div class="no-bus-icon"><i class="fa-solid fa-ban"></i></div>';
        echo '<p>' . htmlspecialchars($translation["No-bus-time"][$lang]) . '</p></div>';
    }
}
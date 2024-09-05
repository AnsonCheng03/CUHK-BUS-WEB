<?php

if (!isset($_POST))
    die();
include_once('../Essential/functions/loadenv.php');

/* Init Program */
date_default_timezone_set("Asia/Hong_Kong");
include('../Essential/functions/functions.php');
$initdataitems = array(
    "Route" => true,
    "Translate" => true,
    "Station" => true,
);
include('../Essential/functions/initdatas.php');


//Bus Status
$busservices = json_decode(file_get_contents(__DIR__ . '/../Data/Status.json'), true);
$temp = array_slice($busservices, -30, 1);
$thirtyminbusservice = array_pop($temp);
$currentbusservices = end($busservices);
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

$noroute = 0;
$lang = $_POST['language'];
$poststart = $_POST['Start'];
$postdest = $_POST['Dest'];
$postmode = $_POST['mode'];
$posttravwk = $_POST['Trav-wk'];
$posttravdt = $_POST['Trav-dt'];
$posttravhr = $_POST['Trav-hr'];
$posttravmin = $_POST['Trav-min'];
$departnowbtn = isset($_POST['deptnow']);

try {
    $conn = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);
    $stmt = $conn->prepare("INSERT INTO `logs` (`Time`, `Webpage`, `Start`, `Dest`, `Mode`, `Departnow`, `Lang`) 
        VALUES (?, 'routesearch', ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("ssssss", $Time, $Startsql, $Destsql, $postmode, $departnowbtn, $lang);
    $Time = (new DateTime())->format('Y-m-d H:i:s');
    $Startsql = $postmode == "building" ? $_POST['Startbd'] : $_POST['Start'];
    $Destsql = $postmode == "building" ? $_POST['Destbd'] : $_POST['Dest'];
    $stmt->execute();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
}

// Get Available Buses
if (!$departnowbtn) {
    foreach ($bus as $index => $busarr) {
        //Base on time
        $currenttime = (DateTime::createFromFormat("H:i", $posttravhr . ":" . $posttravmin))->getTimestamp();
        $starttime = (DateTime::createFromFormat("H:i", $busarr["schedule"][0]))->getTimestamp();
        $endtime = (DateTime::createFromFormat("H:i", $busarr["schedule"][1]))->getTimestamp();
        if ($currenttime < $starttime || $currenttime > $endtime)
            unset($bus[$index]);

        //Base on weekday or day
        if (strpos($busarr["schedule"][3], $posttravdt) === false && $busarr["schedule"][3] !== $posttravdt)
            unset($bus[$index]);
        if (strpos($busarr["schedule"][4], $posttravwk) === false)
            unset($bus[$index]);
    }
} else {
    foreach ($bus as $index => $busarr)
        if (($busarr["stats"]["status"] == "no" && $busarr["stats"]["prevstatus"] != "normal") || ($busarr["stats"]["status"] == "suspended" && $busarr["stats"]["prevstatus"] != "normal"))
            unset($bus[$index]);
}
$bus = array_filter($bus);





//Init
$totalstart = $currstart = $totaldest = $currdest = 0;

//Check later plz

if ($postmode == "building") {

    $poststartbd = strstr($_POST['Startbd'], ' (', true) ?: $_POST['Startbd'];
    $postdestbd = strstr($_POST['Destbd'], ' (', true) ?: $_POST['Destbd'];

    if ($poststartbd == "" || $postdestbd == "")
        die("<h4>" . $translation["warning-noinput"][$lang] . "</h4>");


    //Building name to Building Code
    $flag[0] = $flag[1] = 0;
    foreach (array_reverse($translation) as $buildingcode => $buildingname) {
        foreach ($buildingname as $readablename) {
            if ($readablename == $poststartbd) {
                $poststartbd = $buildingcode;
                $flag[0]++;
            }
            if ($readablename == $postdestbd) {
                $postdestbd = $buildingcode;
                $flag[1]++;
            }
            if ($flag[0] == 1 && $flag[1] == 1)
                break 2;
        }
    }

    //Building to Station
    $startstation = [];
    $deststation = [];
    foreach ($station as $stationcode => $val) {
        foreach ($val as $building) {
            if ($building == $poststartbd) {
                $totalstart++;
                $startstation[] = $stationcode;
            }
            if ($building == $postdestbd) {
                $totaldest++;
                $deststation[] = $stationcode;
            }
        }
    }

    $startstation = array_values(array_filter(array_unique($startstation)));
    $deststation = array_values(array_filter(array_unique($deststation)));

    //Check Error
    if ($totalstart <= 0 || $totaldest <= 0)
        die("<h4>" . $translation["building-error"][$lang] . "</h4>");
} else {
    $startstation = [$poststart];
    $deststation = [$postdest];
}

function searchRoutes($startstation, $deststation, $bus, $translation, $lang)
{
    $totaldest = count($deststation);
    $totalstart = count($startstation);
    $samestation = false;
    $routeresult = [
        "busno" => [],
        "start" => [],
        "end" => [],
        "time" => [],
        "route" => []
    ];

    for ($currdest = 0; $currdest < $totaldest; $currdest++) {
        for ($currstart = 0; $currstart < $totalstart; $currstart++) {
            if ($startstation[$currstart] == $deststation[$currdest]) {
                $samestation = true;
                continue;
            }

            foreach ($bus as $busno => $line) {
                $result = searchDirection($startstation[$currstart], $deststation[$currdest], $busno, $line["stations"]["name"], $line["stations"]["attr"], $line["stations"]["time"], $translation, $lang);
                foreach ($result as $newresult) {
                    $routeresult = mergeRouteResults($routeresult, $newresult);
                }
            }
        }
    }

    return [
        'samestation' => $samestation,
        'routeresult' => $routeresult
    ];
}

function searchDirection($start, $dest, $busno, $line, $attrline, $timeline, $translation, $lang)
{
    $possibilities = [];

    // Find all occurrences of the start station
    $startPositions = array_keys($line, $start);


    foreach ($startPositions as $startPos) {
        // Search for destination stations after each start position
        $searchLine = array_slice($line, $startPos + 1);
        $destPositions = array_keys($searchLine, $dest);

        foreach ($destPositions as $relativeDestPos) {
            // Build route result for this possibility
            $routeResult = buildRouteResult(
                $busno,
                $start,
                $dest,
                $line,
                $attrline,
                $timeline,
                $startPos,
                $relativeDestPos,
                $translation,
                $lang,
            );

            if ($routeResult) {
                $possibilities[] = $routeResult;
            }
        }
    }

    return $possibilities;
}



function buildRouteResult($busno, $start, $dest, $line, $attrline, $timeline, $startIndex, $endIndex, $translation, $lang)
{

    $newline = array_slice($line, $startIndex + 1, $endIndex + 1, true);
    $newlineattr = array_slice($attrline, $startIndex, $endIndex + 2, true);
    $newlinetime = array_slice($timeline, $startIndex, $endIndex + 2, true);
    $newline = array_map(fn($stop) => $translation[$stop][$lang], $newline);

    // function buildStationString($station, $attr, $translation, $lang)
    $startpos = $translation[$start][$lang];
    if (isset($translation[$newlineattr[$startIndex] ?? ""][$lang])) {
        $startpos .= " ({$translation[$newlineattr[$startIndex] ?? ""][$lang]})";
    }
    $endpos = end($newline);
    $endposattr = "";
    if (isset($translation[end($newlineattr) ?? ""][$lang])) {
        $endposattr = " ({$translation[end($newlineattr) ?? ""][$lang]})";
    }

    $route = "$startpos ➤ " . implode(" ➤ ", $newline) . "$endposattr";

    return [
        "busno" => [$busno],
        "start" => [$startpos],
        "end" => [$endpos],
        "time" => [array_sum($newlinetime)],
        "route" => [$route]
    ];
}



function mergeRouteResults($existing, $new)
{
    foreach ($new as $key => $value) {
        $existing[$key] = array_merge($existing[$key], $value);
    }
    return $existing;
}

$searchResult = searchRoutes($startstation, $deststation, $bus, $translation, $lang);

if (empty($searchResult['routeresult'])) {
    $searchResult['routeresult']["busno"] = array("N/A");
    $searchResult['routeresult']["route"] = array($translation["No-BUS"][$lang] . ($departnowbtn ? "<br><br>" . ($bus ? $translation["warning-showbus"][$lang] . implode(", ", array_keys($bus)) : $translation['stop-running'][$lang]) : ""));
    $noroute = 1;
}

//Group Result
foreach ($searchResult['routeresult']["busno"] as $index => $startloc) {
    //time process
    ($searchResult['routeresult']["time"][$index] == 0) ? $timeoutput = "N/A" : $timeoutput = round($searchResult['routeresult']["time"][$index] / 60);
    $routegroupresult[$searchResult['routeresult']["start"][$index]][$searchResult['routeresult']["end"][$index]]["busno"][] = $searchResult['routeresult']["busno"][$index];
    $routegroupresult[$searchResult['routeresult']["start"][$index]][$searchResult['routeresult']["end"][$index]]["route"][] = $searchResult['routeresult']["route"][$index];
    $routegroupresult[$searchResult['routeresult']["start"][$index]][$searchResult['routeresult']["end"][$index]]["timeused"][] = $timeoutput;
}
?>

<?php
// Sort the results by time
$sortedResults = [];
foreach ($routegroupresult as $start => $temp) {
    foreach ($temp as $end => $busroutes) {
        foreach ($busroutes["busno"] as $index => $busno) {
            $time = $busroutes["timeused"][$index];
            if ($time == "N/A") {
                $time = PHP_INT_MAX; // Put "N/A" times at the end
            }
            $sortedResults[] = [
                'time' => $time,
                'busno' => $busno,
                'start' => $start,
                'end' => $end,
                'route' => $busroutes["route"][$index],
                'timeDisplay' => $busroutes["timeused"][$index]
            ];
        }
    }
}

// Sort the results
usort($sortedResults, function ($a, $b) {
    return $a['time'] <=> $b['time'];
});

// Display the sorted results
echo '<table id="routeresult" cellspacing="1" cellpadding="10">
    <tr style="background-color: #009879; color: #ffffff; text-align: center;">
        <td>' . $translation["table-line"][$lang] . ' | ' . $translation["time-heading-arriving"][$lang] . '</td>
    </tr>';

if ($searchResult['samestation']) {
    echo '<tr style="background-color: red; color: #ecf0f1; text-align: center;">
        <td>' . $translation["searchResult['samestation']-info"][$lang] . '</td>
    </tr>';
}

foreach ($sortedResults as $result) {
    $busnostr = explode("→", $result['busno']);
    // if ($busnostr[0] . "#" != $busnostr[1] && $busnostr[1] . "#" != $busnostr[0]) {
    echo '<tr style="background-color: #ecf0f1;">
            <td>
                <div>' . $result['busno'] . ' | ' . $result['timeDisplay'] . ' min</div>
                <div>' . $result['start'] . ' → ' . $result['end'] . '</div>
                <div>' . $result['route'] . '</div>';

    if ($departnowbtn) {
        if (
            ($bus[$busnostr[0]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[0]]["stats"]["status"] == "no") ||
            ($bus[$busnostr[1]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[1]]["stats"]["status"] == "no")
        ) {
            echo '<div><span class="eoswarning">' . $translation["justeos-warning"][$lang] . '</span></div>';
        }
    }

    if ($result['timeDisplay'] != "N/A") {
        echo "<div><button class='detailsbtn' onclick=\"localStorage.setItem('startingpt', '" . $result['start'] . "'); append_query('mode', 'realtime');\">" . $translation['table-detals'][$lang] . "</button></div>";
    }

    echo '</td></tr>';
    // }
}

echo '</table>';
?>
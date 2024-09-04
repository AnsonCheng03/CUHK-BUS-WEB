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
$thirtyminbusservice = array_pop(array_slice($busservices, -30, 1));
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
$forceshowexchange = isset($_POST['showallroute']);
$departnowbtn = isset($_POST['deptnow']);

try {
    $conn = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);
    $stmt = $conn->prepare("INSERT INTO `logs` (`Time`, `Webpage`, `Start`, `Dest`, `Mode`, `Showallroute`, `Departnow`, `Lang`) 
        VALUES (?, 'routesearch', ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("sssssss", $Time, $Startsql, $Destsql, $postmode, $forceshowexchange, $departnowbtn, $lang);
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
$samestation = false;
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

$samestation = false;

for ($currdest = 0; $currdest < $totaldest; $currdest++) {
    for ($currstart = 0; $currstart < $totalstart; $currstart++) {
        if ($startstation[$currstart] == $deststation[$currdest]) {
            $samestation = true;
            continue;
        }

        foreach ($bus as $busno => $line) {
            $attrline = $line["stations"]["attr"];
            $timeline = $line["stations"]["time"];
            $line = $line["stations"]["name"];

            $directions = [
                ['search' => array_reverse($line, true)],
                ['search' => $line]
            ];

            foreach ($directions as $direction) {
                $busstopnum = array_search($startstation[$currstart], $direction['search']);
                if ($busstopnum === false)
                    continue;

                $searchline = array_slice($line, $busstopnum + 1);
                $searchlineattr = array_slice($attrline, $busstopnum);
                $searchlinetime = array_slice($timeline, $busstopnum);

                $busendstation = array_search($deststation[$currdest], $searchline);
                if ($busendstation === false)
                    continue;

                $newline = array_slice($searchline, 0, $busendstation + 1, true);
                $newlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                $newlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);

                $routeresult["busno"][] = $busno;
                $newline = array_map(fn($stop) => $translation[$stop][$lang], $newline);

                $startpos = $translation[$startstation[$currstart]][$lang];
                $startpos .= isset($translation[$newlineattr[0]][$lang]) ? " ({$translation[$newlineattr[0]][$lang]})" : "";

                $endpos = end($newline);
                $endpos .= isset($translation[end($newlineattr)][$lang]) ? " ({$translation[end($newlineattr)][$lang]})" : "";

                $routeresult["start"][] = $startpos;
                $routeresult["end"][] = $endpos;
                $routeresult["time"][] = array_sum($newlinetime);

                $route = "$startpos ➤ " . implode(" ➤ ", $newline);
                if (isset($bus[$busno . "#"]) || strpos($busno, "#") !== false) {
                    $route .= "<br><br><span class=\"departtime\">{$translation["info-sch"][$lang]}{$bus[$busno]["schedule"][2]}</span>";
                }
                $routeresult["route"][] = $route;

                break;  // Exit both foreach loops if a route is found
            }
        }
    }
}

if (empty($routeresult)) {
    $routeresult["busno"] = array("N/A");
    $routeresult["route"] = array($translation["No-BUS"][$lang] . ($departnowbtn ? "<br><br>" . ($bus ? $translation["warning-showbus"][$lang] . implode(", ", array_keys($bus)) : $translation['stop-running'][$lang]) : ""));
    $noroute = 1;
}

//Group Result
foreach ($routeresult["busno"] as $index => $startloc) {
    //time process
    ($routeresult["time"][$index] == 0) ? $timeoutput = "N/A" : $timeoutput = round($routeresult["time"][$index] / 60);
    $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["busno"][] = $routeresult["busno"][$index];
    $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["route"][] = $routeresult["route"][$index];
    $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["timeused"][] = $timeoutput;
}
?>

<!--Route Result!-->
<?php



echo
    '<table id="routeresult" cellspacing="1" cellpadding="10">
        <tr style="background-color: #009879; color: #ffffff; text-align: center;">
        <td style="width: 45px">' . $translation["table-line"][$lang] . '</td>
        <td>' . $translation["table-route"][$lang] . '</td>
        <td style="width: 60px">' . $translation["table-detals"][$lang] . '</td>
        </tr>';

if ($samestation) {
    echo '<tr style="background-color: red; color: #ecf0f1; text-align: center;">
            <td colspan="3">' . $translation["samestation-info"][$lang] . '</td>
            </tr>';
}

foreach ($routegroupresult as $start => $temp) {
    foreach ($temp as $end => $temp) {
        foreach ($temp as $exchange => $busroutes) {
            if ($start !== "") {
                echo '<tr style="background-color: #c7ecee;"><td colspan="3" style="padding-left:15px;">';
                if ($exchange === "") {
                    echo $start . " → " . $end;
                } else {
                    echo $start . " → " . $exchange . " → " . $end;
                }
                echo '</td></tr>';
            }

            foreach ($busroutes["busno"] as $index => $busno) {
                $busnostr = [explode("→", $busno)[0], explode("→", $busno)[1]];
                if ($busnostr[0] . "#" != $busnostr[1] && $busnostr[1] . "#" != $busnostr[0]) {
                    echo '<tr style="background-color: #ecf0f1;">
                            <td style="text-align:center;">' .
                        $busno .
                        '</td>
                            <td>' .
                        $busroutes["route"][$index] . "<br>";
                    if ($departnowbtn)
                        if (($bus[$busnostr[0]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[0]]["stats"]["status"] == "no") || ($bus[$busnostr[1]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[1]]["stats"]["status"] == "no"))
                            echo '<br><span class="eoswarning">' . $translation["justeos-warning"][$lang] . '</span>';
                    echo '</td>
                            <td style="text-align:center;">' .
                        $translation["time-heading-arriving"][$lang] . "<br>" . $busroutes["timeused"][$index] . " min<br>";
                    if ($busroutes["timeused"][$index] != "N/A")
                        echo "<button class='detailsbtn' onclick=\" localStorage.setItem('startingpt', '" . $start . "'); append_query('mode', 'realtime'); \">" . $translation['table-detals'][$lang] . "</button>";
                    echo '</td>
                            </tr> ';
                }
            }
        }
    }
}
echo '</table>';



?>
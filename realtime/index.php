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
        if (strpos($busarr["schedule"][4], "WK-".(new DateTime())->format('D')) === false)
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

foreach ($outputschedule as $stationname => $schedule) {
    foreach ($schedule as $busno => $timetable) {
        if(isset($bus[$busno])) {
        echo "
        <div class='bussect'>
            <div class='busname'>" . $busno . "</div>";
        $outputcountcount = 0;
        $currtime = (new DateTime())->modify("-30 minutes")->format('H:i:s');

        foreach ($timetable as $time) {

            if ($time >= $currtime) {
                echo "<div class='bustype'>";
                    echo "<div class='businfo'>".
                        (explode("|", $stationname)[1] ? $translation[explode("|", $stationname)[1]][$lang] : $translation["mode-realtime"][$lang] ).
                    "</div>";
                    echo "<div class='arrtime'>".$time."</div>";
                echo "</div>";
                $outputcountcount++;
            }
            if ($outputcountcount >= 5) break;
        }
        echo "
        </div>
        ";
    }
    }
}

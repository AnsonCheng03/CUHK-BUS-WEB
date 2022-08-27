<?php

if (!isset($_POST))
    die();

/* Init Program */
include('../Essential/functions/functions.php');

foreach (csv_to_array(__DIR__ . "/../Data/Route") as $busno) {
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
foreach (array_slice(csv_to_array(__DIR__ . "/../Data/Translate"), 1) as $row) {
    if ($row[0] !== "" && substr($row[0], 0, 2) !== "//") {
        $translation[$row[0]] = array($row[2], $row[3]);
        $translation[$row[0]][] = $row[1];
    }
}

//Buildings
foreach (array_slice(csv_to_array(__DIR__ . "/../Data/Station"), 1) as $row) {
    $station[$row[1]][] = $row[0];
}

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



// Get Available Buses
if (!isset($_POST['deptnow'])) {
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


    //Update Current bus
    $operating = array_keys(array_filter($bus, function ($busarr) {
        return $busarr["stats"]["status"] == "normal" || $busarr["stats"]["status"] == "delay";
    }));

    if ($operating)
        echo '<script> document.querySelector(".show-time").innerHTML = "' . $translation["info-running"][$lang] . implode(", ", $operating) . '"; document.currentScript.remove();</script>';
    else
        echo '<script> document.querySelector(".show-time").innerHTML = "' . $translation["stop-running"][$lang] . '"; document.currentScript.remove();</script>';
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
            if ($flag[0] == 1 && $flag[1] == 1) break 2;
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


do {
    do {
        if ($startstation[$currstart] == $deststation[$currdest]) $samestation = true;

        //Startline Bus (Test : 39區 → 善衡書院)
        foreach ($bus as $busno => $line) {
            //Get start station index
            $successful = 0;
            $attrline = $line["stations"]["attr"];
            $timeline = $line["stations"]["time"];
            $line = $line["stations"]["name"];
            $busstopnum = array_search($startstation[$currstart], array_reverse($line, true));
            if ($busstopnum !== false) {
                //Get route after start station
                $searchline = array_slice($line, $busstopnum + 1);
                $searchlineattr = array_slice($attrline, $busstopnum);
                $searchlinetime = array_slice($timeline, $busstopnum);

                //Get end station index
                $busendstation = array_search($deststation[$currdest], $searchline);
                if ($busendstation !== false) {
                    $noroute = false;
                    $successful = 1;
                    //Trim $line
                    $newline = array_slice($searchline, 0, $busendstation + 1, true);
                    $newlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                    $newlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                    //Output
                    $routeresult["busno"][] = $busno;
                    foreach ($newline as $stopindex => $stop) {
                        $newline[$stopindex] = $translation[$stop][$lang];
                    }

                    //Start
                    if ($translation[$newlineattr[0]][$lang]) {
                        $startpos = $translation[$startstation[$currstart]][$lang] . " (" . $translation[$newlineattr[0]][$lang] . ")";
                    } else {
                        $startpos = $translation[$startstation[$currstart]][$lang];
                    }

                    //End
                    if ($translation[end($newlineattr)][$lang]) {
                        $endpos = end($newline) . " (" . $translation[end($newlineattr)][$lang] . ")";
                    } else {
                        $endpos = end($newline);
                    }

                    $routeresult["start"][] = $startpos;
                    $routeresult["exchange"][] = "";
                    $routeresult["end"][] = $endpos;
                    $routeresult["time"][] = array_sum($newlinetime);
                    if (isset($bus[$busno . "#"]) || strpos($busno, "#") !== false) {
                        $routeresult["route"][] = $startpos . " ➤ " . implode(" ➤ ", $newline) . "<br><br><span class=\"departtime\">" . $translation["info-sch"][$lang] . $bus[$busno]["schedule"][2] . "</span>";
                    } else {
                        $routeresult["route"][] = $translation[$startstation[$currstart]][$lang] . " ➤ " . implode(" ➤ ", $newline);
                    }
                }

                if ($successful == 0) {
                    //try again with ascending
                    $busstopnum = array_search($startstation[$currstart], $line);
                    if ($busstopnum !== false) {
                        //Get route after start station
                        $searchline = array_slice($line, $busstopnum + 1);
                        $searchlineattr = array_slice($attrline, $busstopnum);
                        $searchlinetime = array_slice($timeline, $busstopnum);

                        //Get end station index
                        $busendstation = array_search($deststation[$currdest], $searchline);
                        if ($busendstation !== false) {
                            $noroute = false;
                            //Trim $line
                            $newline = array_slice($searchline, 0, $busendstation + 1, true);
                            $newlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                            $newlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);

                            //Output
                            $routeresult["busno"][] = $busno;
                            foreach ($newline as $stopindex => $stop) {
                                $newline[$stopindex] = $translation[$stop][$lang];
                            }

                            //Start
                            if ($translation[$newlineattr[0]][$lang]) {
                                $startpos = $translation[$startstation[$currstart]][$lang] . " (" . $translation[$newlineattr[0]][$lang] . ")";
                            } else {
                                $startpos = $translation[$startstation[$currstart]][$lang];
                            }

                            //End
                            if ($translation[end($newlineattr)][$lang]) {
                                $endpos = end($newline) . " (" . $translation[end($newlineattr)][$lang] . ")";
                            } else {
                                $endpos = end($newline);
                            }

                            $routeresult["start"][] = $startpos;
                            $routeresult["exchange"][] = "";
                            $routeresult["end"][] = $endpos;
                            $routeresult["time"][] = array_sum($newlinetime);
                            if (isset($bus[$busno . "#"]) || strpos($busno, "#") !== false) {
                                $routeresult["route"][] = $translation[$startstation[$currstart]][$lang] . " ➤ " . implode(" ➤ ", $newline) . "<br><br><span class=\"departtime\">" . $translation["info-sch"][$lang] . $bus[$busno]["schedule"][2] . "</span>";
                            } else {
                                $routeresult["route"][] = $translation[$startstation[$currstart]][$lang] . " ➤ " . implode(" ➤ ", $newline);
                            }
                        }
                    }
                }
            }
        }


        //No Direct Route (Test : 39區 → 敬文書院 → 地鐵大學站廣場)
        if ($noroute || $forceshowexchange) {

            foreach ($bus as $busno => $line) {
                //Get start station index
                $attrline = $line["stations"]["attr"];
                $timeline = $line["stations"]["time"];
                $line = $line["stations"]["name"];
                $busstopnum = array_search($startstation[$currstart], $line);
                if ($busstopnum !== false) {
                    //Get route after start station
                    $searchline = array_slice($line, $busstopnum + 1);
                    $searchlineattr = array_slice($attrline, $busstopnum);
                    $searchlinetime = array_slice($timeline, $busstopnum);

                    foreach ($searchline as $station) {

                        //Let $Station as Start
                        foreach ($bus as $newbusno => $newline) {
                            //Get start station index
                            $successful = 0;
                            $newlineattr = $newline["stations"]["attr"];
                            $newlinetime = $newline["stations"]["time"];
                            $newline = $newline["stations"]["name"];
                            $newbusstopnum = array_search($station, array_reverse($newline, true));
                            if ($newbusstopnum !== false) {
                                //Get route after start station
                                $newsearchline = array_slice($newline, $newbusstopnum + 1);
                                $newsearchlineattr = array_slice($newlineattr, $newbusstopnum);
                                $newsearchlinetime = array_slice($newlinetime, $newbusstopnum);

                                //Get end station index
                                $busendstation = array_search($station, $searchline);
                                $secbusendstation = array_search($deststation[$currdest], $newsearchline);
                                if ($secbusendstation !== false && $busendstation !== false) {
                                    $successful = 1;
                                    $routeresult["busno"][] = $busno . "→" . $newbusno;

                                    //Trim $line & $newsearchline
                                    $searchline = array_slice($searchline, 0, $busendstation + 1, true);
                                    $newsearchline = array_slice($newsearchline, 0, $secbusendstation + 1, true);
                                    $searchlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                                    $newsearchlineattr = array_slice($newsearchlineattr, 0, $secbusendstation + 2, true);
                                    $searchlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                                    $newsearchlinetime = array_slice($newsearchlinetime, 0, $secbusendstation + 2, true);

                                    foreach ($searchline as $stopindex => $stop) {
                                        $searchline[$stopindex] = $translation[$stop][$lang];
                                    }
                                    foreach ($newsearchline as $stopindex => $stop) {
                                        $newsearchline[$stopindex] = $translation[$stop][$lang];
                                    }

                                    //Start
                                    if ($translation[$searchlineattr[0]][$lang]) {
                                        $startpos = $translation[$startstation[$currstart]][$lang] . " (" . $translation[$searchlineattr[0]][$lang] . ")";
                                    } else {
                                        $startpos = $translation[$startstation[$currstart]][$lang];
                                    }

                                    //Exchange End
                                    if ($translation[end($searchlineattr)][$lang]) {
                                        $exchangeposen = end($searchline) . " (" . $translation[end($searchlineattr)][$lang] . ")";
                                    } else {
                                        $exchangeposen = end($searchline);
                                    }

                                    //Exchange Start
                                    if ($translation[$newsearchlineattr[0]][$lang]) {
                                        $exchangeposst = end($searchline) . " (" . $translation[$newsearchlineattr[0]][$lang] . ")";
                                    } else {
                                        $exchangeposst = end($searchline);
                                    }

                                    //Exchange Group
                                    if ($exchangeposen == $exchangeposst) {
                                        $exchangepos = $exchangeposen;
                                    } else {
                                        $exchangepos = $exchangeposen . " → " . $exchangeposst;
                                    }

                                    //End
                                    if ($translation[end($newsearchlineattr)][$lang]) {
                                        $endpos = end($newsearchline) . " (" . $translation[end($newsearchlineattr)][$lang] . ")";
                                    } else {
                                        $endpos = end($newsearchline);
                                    }

                                    $routeresult["start"][] = $startpos;
                                    $routeresult["exchange"][] = $exchangepos;
                                    $routeresult["end"][] = $endpos;
                                    $routeresult["time"][] = array_sum($searchlinetime) + array_sum($newsearchlinetime);
                                    $routeresult["route"][] = $translation[$startstation[$currstart]][$lang] . " ➤ " . implode(" ➤ ", $searchline) . " <br> 【 " . $translation["table-transfer"][$lang] . $exchangepos . " 】 <br> " . $translation[$station][$lang] . " ➤ " . implode(" ➤ ", $newsearchline);
                                }

                                if ($successful == 0) {
                                    //try again with ascending
                                    $newbusstopnum = array_search($station, $newline);
                                    if ($newbusstopnum !== false) {
                                        //Get route after start station
                                        $newsearchline = array_slice($newline, $newbusstopnum + 1);
                                        $newsearchlineattr = array_slice($newlineattr, $newbusstopnum);
                                        $newsearchlinetime = array_slice($newlinetime, $newbusstopnum);

                                        //Get end station index
                                        $busendstation = array_search($station, $searchline);
                                        $secbusendstation = array_search($deststation[$currdest], $newsearchline);

                                        if ($secbusendstation !== false && $busendstation !== false) {
                                            $successful = 1;
                                            $routeresult["busno"][] = $busno . "→" . $newbusno;

                                            //Trim $line & $newsearchline
                                            $searchline = array_slice($searchline, 0, $busendstation + 1, true);
                                            $newsearchline = array_slice($newsearchline, 0, $secbusendstation + 1, true);
                                            $searchlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                                            $newsearchlineattr = array_slice($newsearchlineattr, 0, $secbusendstation + 2, true);
                                            $searchlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                                            $newsearchlinetime = array_slice($newsearchlinetime, 0, $secbusendstation + 2, true);

                                            foreach ($searchline as $stopindex => $stop) {
                                                $searchline[$stopindex] = $translation[$stop][$lang];
                                            }
                                            foreach ($newsearchline as $stopindex => $stop) {
                                                $newsearchline[$stopindex] = $translation[$stop][$lang];
                                            }

                                            //Start
                                            if ($translation[$searchlineattr[0]][$lang]) {
                                                $startpos = $translation[$startstation[$currstart]][$lang] . " (" . $translation[$searchlineattr[0]][$lang] . ")";
                                            } else {
                                                $startpos = $translation[$startstation[$currstart]][$lang];
                                            }

                                            //Exchange End
                                            if ($translation[end($searchlineattr)][$lang]) {
                                                $exchangeposen = end($searchline) . " (" . $translation[end($searchlineattr)][$lang] . ")";
                                            } else {
                                                $exchangeposen = end($searchline);
                                            }

                                            //Exchange Start
                                            if ($translation[$newsearchlineattr[0]][$lang]) {
                                                $exchangeposst = end($searchline) . " (" . $translation[$newsearchlineattr[0]][$lang] . ")";
                                            } else {
                                                $exchangeposst = end($searchline);
                                            }

                                            //Exchange Group
                                            if ($exchangeposen == $exchangeposst) {
                                                $exchangepos = $exchangeposen;
                                            } else {
                                                $exchangepos = $exchangeposen . " → " . $exchangeposst;
                                            }

                                            //End
                                            if ($translation[end($newsearchlineattr)][$lang]) {
                                                $endpos = end($newsearchline) . " (" . $translation[end($newsearchlineattr)][$lang] . ")";
                                            } else {
                                                $endpos = end($newsearchline);
                                            }

                                            $routeresult["start"][] = $startpos;
                                            $routeresult["exchange"][] = $exchangepos;
                                            $routeresult["end"][] = $endpos;
                                            $routeresult["time"][] = array_sum($searchlinetime) + array_sum($newsearchlinetime);
                                            $routeresult["route"][] = $translation[$startstation[$currstart]][$lang] . " ➤ " . implode(" ➤ ", $searchline) . " <br> 【 " . $translation["table-transfer"][$lang] . $translation[$station][$lang] . " 】 <br> " . $translation[$station][$lang] . " ➤ " . implode(" ➤ ", $newsearchline);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $currstart++;
    } while ($currstart < $totalstart);
    $currstart = 0;
    $currdest++;
} while ($currdest < $totaldest);


if (empty($routeresult)) {
    $routeresult["busno"] = array("N/A");
    $routeresult["route"] = array($translation["No-BUS"][$lang] . (isset($_POST['deptnow']) ? "<br><br>" . ($bus ? $translation["warning-showbus"][$lang] . implode(", ", array_keys($bus)) : $translation['stop-running'][$lang]) : ""));
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
                    if (isset($_POST['deptnow']))
                        if (($bus[$busnostr[0]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[0]]["stats"]["status"] == "no") || ($bus[$busnostr[1]]["stats"]["prevstatus"] == "normal" && $bus[$busnostr[1]]["stats"]["status"] == "no"))
                            echo '<br><span class="eoswarning">' . $translation["justeos-warning"][$lang] . '</span>';
                    echo  '</td>
                            <td style="text-align:center;">' .
                                $translation["time-heading-arriving"][$lang] . "<br>" . $busroutes["timeused"][$index] . " min<br>";
                    if($busroutes["timeused"][$index] != "N/A")    
                        echo "<button class='detailsbtn' onclick=\" localStorage.setItem('startingpt', '".$start."'); append_query('mode', 'realtime'); \">".$translation['table-detals'][$lang]."</button>";
                    echo    '</td>
                            </tr> ';
                }
            }
        }
    }
}
echo '</table>';



?>
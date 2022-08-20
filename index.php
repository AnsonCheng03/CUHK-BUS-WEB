<?php 
  $version = "5.0.10";
?>

<html>
    <head>
        <title>CUHK BUS</title>
        <meta name="title" content="中大巴士資訊站 CUHK BUS INFOPAGE">
        <meta name="description" content="中大巴士資訊站提供點對點路線搜尋、實時校巴查詢服務，讓你輕鬆在中大校園穿梭。 CUHK Bus Infopage provides point-to-point route search and real-time school bus query services, allowing you to travel around the CUHK campus easily.">
        <meta name="keywords" content="CUHK, 中大, 香港中文大學, The Chinese University of Hong Kong, BUS, CUBUS, 巴士, 校巴, School Bus, 路線, route, 校巴站, busstop">
        <meta name="robots" content="index, follow">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="revisit-after" content="7 days">
        <meta name="author" content="Anson Cheng">
        <link rel="icon" href="Images/bus.ico" type="image/x-icon">
        <link rel="shortcut icon" href="Images/bus.ico" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#ecf0f1">
        <meta name="application-name" content="CU BUS">
        <meta name="msapplication-TileColor" content="#2196f3">
        <meta name="msapplication-TileImage" content="Images/bus.jpg">
        <meta name="msapplication-config" content="/assets/favicons/browserconfig.xml">
        <meta name="msapplication-navbutton-color" content="#2196f3">
        <meta name="apple-touch-fullscreen" content="yes" />
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="CU BUS" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="apple-touch-icon" href="Images/bus.ico"/>
        <link href="Images/splashscreens/iphone5.jpg" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/iphone6.jpg" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/iphoneplus.jpg" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/iphonex.jpg" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/iphonexr.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/iphonexsmax.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/ipad.jpg" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/ipadpro1.jpg" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/ipadpro3.jpg" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="Images/splashscreens/ipadpro2.jpg" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <meta name="MobileOptimized" content="320" />
        <script type="text/javascript" src="Essential/jquery.js?v=<?php echo $version?>"></script>
        <link rel="stylesheet" href="Essential/mainpage.css?v=<?php echo $version?>">
        <link rel="stylesheet" href="Essential/component.css?v=<?php echo $version?>">
        <script src="Essential/mainpage.js?v=<?php echo $version?>"></script>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3579541618707661" crossorigin="anonymous"></script>
    </head>

    <body>
      <!-- Ajax Loader !-->
        <div id="progress"></div>
      <!--Functions, Init, Data-->
        <?php
          //functions
          function runjs($output, $script = "console.log", $with_script_tags = true) {
              $js_code = $script.'(' . json_encode($output, JSON_HEX_TAG) . ');';
              if ($with_script_tags) {
                  $js_code = '<script>' . $js_code . '</script>';
              }
              echo $js_code;
          }

            function urlquery($key, $default = '', $data_type = '')
            {
                $param = (isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default);

                if (!is_array($param) && $data_type == 'int') {
                    $param = intval($param);
                }

                return $param;
            }

            function download_files($filename, $url, $skipfilesize = false, $forcedownload = false){
              if (file_exists($filename)) $moditime = filemtime($filename); else $moditime = 0;
              if (file_exists($filename)) $filesize = filesize($filename); else $filesize = 0;

              if( $skipfilesize == true ) { $filesize = 10000; }
              
              if(($moditime+30*60)<=time() || $filesize < 300 || $forcedownload){
                $source = file_get_contents($url);
                file_put_contents($filename, $source);
              }
            }

            function csv_to_array($filename) {
              $arr=array();
              $row = -1;
              if (($handle = fopen($filename.".csv", "r")) !== FALSE) {
                  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                      $num = count($data);
                      $row++;
                      for ($c = 0; $c < $num; $c++) {
                          $arr[$row][$c]= $data[$c];
                      }
                  }
                  fclose($handle);
              }
              return $arr;
            }

            function pingAddress($ip) {
              if($socket =@ fsockopen($ip, 443, $errno, $errstr, 30)) {
                return true;
                fclose($socket);
                } else {
                return false;
                }
            }

            function alert($img, $msg){
              $icon = glob("./Images/".strtolower($img).".*");
              $content = $msg;
              if(isset($icon[0]) && $content != ""){
                echo '<div class="alert-box">'.
                  '<table width="100%"><tr>'.
                    '<td width=100px style="text-align:center; margin: 10px auto;">'.
                      '<img src="'.$icon[0].'" width="50%">'.
                    '</td>'.
                    '<td>'.
                      $content.
                    '</td>'.
                  '</tr></table></div>';
              }
            }

          //Init Program
            date_default_timezone_set("Asia/Hong_Kong"); 
            $error = false;
            $fetcherror = false;
            $noroute = 0;
            if(urlquery("lang") == "en") $lang = 1; else $lang = 0;
            $mode = 1;
            if(urlquery("mode") == "station") $mode = 0;
            if(urlquery("mode") == "realtime") $mode = 2;
            download_files("Data/Route.csv",'https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1092784054&single=true&output=csv');
            download_files("Data/Translate.csv",'https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=13523980&single=true&output=csv');
            download_files("Data/Station.csv","https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1424772552&single=true&output=csv");
            download_files("Data/Notice.csv","https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1434460943&single=true&output=csv",true);
            download_files("Data/GPS.csv","https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1636019387&single=true&output=csv",true);


          //Datas
            //Routes
            foreach(csv_to_array("Data/Route") as $busno){
              $bus[$busno[0]]["schedule"] = array($busno[1], $busno[2], $busno[3], $busno[4], $busno[5]);
              foreach (array_filter(array_slice($busno, 6)) as $key => $value) {
                $statnm = strstr($value, '|', true) ?: $value;
                $attr = substr(strstr($value, '|', false),1) ?: "NULL";
                $time = substr(strstr($attr, '|', false),1) ?: "0";
                $attr = strstr($attr, '|', true) ?: "NULL";
                $bus[$busno[0]]["stations"]["name"][] = $statnm;
                $bus[$busno[0]]["stations"]["attr"][] = $attr;
                $bus[$busno[0]]["stations"]["time"][] = floatval($time);
              }
            }


            //Website Translation
            foreach (array_slice(csv_to_array("Data/Translate"), 1) as $row){
              if($row[0] !== "" && substr($row[0], 0, 2) !== "//"){
                $translation[$row[0]] = array( $row[2], $row[3] );
                $translation[$row[0]][] = $row[1];
              }
            }

            //Buildings
            foreach (array_slice(csv_to_array("Data/Station"), 1) as $row){
              $station[$row[1]][] = $row[0];
            }

            //Notice
            foreach (array_slice(csv_to_array("Data/Notice"), 1) as $index => $row){
              $notice[$index]["content"] = array_slice($row, 1);
              $notice[$index]["pref"]["type"] = $row[0];
            }

            //GPS
            foreach (array_slice(csv_to_array("Data/GPS"), 1) as $row){
              $GPS[$row[0]]["Lat"] = $row[1];
              $GPS[$row[0]]["Lng"] = $row[2];
            }

            //Calclate Arrival time
            if($mode == 2){
              foreach ($bus as $busnum => $busno) {
                //Create Timeslots
                $starthr = intval(preg_replace('/[^0-9.]+/', '',  strstr($busno["schedule"][0], ':', true)));
                $startmin = intval(preg_replace('/[^0-9.]+/', '',  substr(strstr($busno["schedule"][0], ':', false),1)));
                $endhr = intval(preg_replace('/[^0-9.]+/', '',  strstr($busno["schedule"][1], ':', true)));
                $endmin = intval(preg_replace('/[^0-9.]+/', '',  substr(strstr($busno["schedule"][1], ':', false),1)));
                $alltime = explode(',',$busno["schedule"][2]);
                foreach ($alltime as $starttime) {
                  $starttime = intval(preg_replace('/[^0-9.]+/', '',  $starttime));
                  for ($i=$starthr; $i<=$endhr; $i++ ){
                    if(($i!==$starthr && $i!==$endhr) || ($i==$endhr && $endmin >= $starttime) || ($i==$starthr && $startmin <= $starttime)){ 
                      $starthour = str_pad($i, 2, '0', STR_PAD_LEFT); 
                      $starttime = str_pad($starttime, 2, '0', STR_PAD_LEFT); 
                      $timetable[] = strtotime(DateTime::createFromFormat('H:i', $starthour.":".$starttime)->format('Y-m-d H:i:s'));
                    }
                  }
                }
                sort($timetable);
                foreach ($timetable as $timeindex => $timevalue){
                  foreach ($busno["stations"]["name"] as $index => $stationname) {
                    if($index == 0) $bus[$busnum]["stations"]["arrivaltime"][$index][$timeindex] = $timevalue;
                    if($index > 0) $bus[$busnum]["stations"]["arrivaltime"][$index][$timeindex] = $bus[$busnum]["stations"]["time"][$index] + $bus[$busnum]["stations"]["arrivaltime"][$index - 1][$timeindex];
                  }
                }
                unset($timetable);
              }
            }
            //Output: $bus[$busnum]["stations"]["arrivaltime"][stationindex][$timeindex]
        ?> 
        <script>
          //php to js
          var Translation = [];
          <?php foreach ($translation as $name => $value) {
            if (end($value) !== "") {
              echo 'Translation["'.$name.'"] = "'.$value[$lang].'";';
            }
          }
          ?>

          var GPSdata = [<?php foreach ($GPS as $name => $location) {
            $nameattr = substr(strstr($name, '|', false),1) ?: "";
            $name = strstr($name, '|', true) ?: $name; 
            if($location["Lat"].$location["Lng"] !== "") echo "{ \"location\": \"".$translation[$name][$lang]."\",  \"lat\": \"".$location["Lat"]."\",  \"lng\": \"".$location["Lng"]."\", \"attr\" : \"".$translation[$nameattr][$lang]."\" , \"code\" : \"".$name."\"  }, ";}?>];
        </script>

      <!--Select Language && Function Buttons-->
        <div style="position: absolute;top: 15px; right: 15px;">
          <button class="lang-selector" onClick="lang_change('tc');"/>中文</button>
          <button class="lang-selector" onClick="lang_change('en');"/>ENG</button>
        </div>

        <div style="position: absolute;top: 15px; left: 15px;">
          <button id="refresh-btn" class="lang-selector" style="display: none;" onClick="window.location.reload();"/><?php echo $translation["refresh-btn"][$lang] ?></button>
        </div>

      <!--Title & Alert-->
        <br><br>
        <h1><?php echo $translation["WEB-Title"][$lang] ?></h1>
        <h2>v. <?php echo $version."<br> ( ".$translation["last-update"][$lang].date("d/m/Y H:i",filemtime("Data/Station.csv"))." )" ?></h2> 

        <?php
          foreach ($notice as $noti){
            alert($noti["pref"]["type"], $noti["content"][$lang]);
          }
        ?>

      <!--Fetch Busstop && Buildings to Array!-->
        <?php
          $filename = "Data/Status.csv";
          if (file_exists($filename)) {$moditime = filemtime($filename);} else {$moditime = 0;}
          $host = 'www.transport.cuhk.edu.hk'; 
          $hostip = gethostbyname($host);

          if(($moditime+3*60)<=time()){
            if(pingAddress($hostip)){   
                //fetch website
                $html = file_get_contents('https://'.$host.'/', 0, stream_context_create(["http"=>["timeout"=>7]]));
                if($html === false ) {
                  $fetcherror = true;
                  alert("alert",$translation["fetch-error"][$lang]." (".$host.")");
                  $savestatus["ERROR"] = "fetch";
                } else {
                  $dom = new DOMDocument();
                  libxml_use_internal_errors(true);
                  $dom->loadHTML($html);
                  $xpath = new DOMXPath($dom);

                  //output all routes with status
                  $nodes = $xpath->query('//*[contains(@class, "home-route") and not(contains(@class, "-col-"))]');
                  foreach($nodes as $html) {
                    //Bus name
                    $busln = $html->getAttribute("href");
                    $buslineurl[] = $busln;
                    $busno = str_replace('https://www.transport.cuhk.edu.hk', '', $busln);
                    $busno = str_replace('tc', '', $busno);
                    $busno = str_replace('route', '', $busno);
                    $busno = strtoupper(str_replace('/', '', $busno));

                    //Bus Status
                    $status = $dom->saveXML($html);
                    if(strpos($status,"hr-status hr-status-delayed") !== false) {$status = "delayed";} else {
                    if(strpos($status,"hr-status hr-status-suspended") !== false) {$status = "suspended";} else {
                    if(strpos($status,"hr-status hr-status-normal") !== false) {$status = "normal";} else {
                    if(strpos($status,"hr-status hr-status-no") !== false) {$status = "no";}}}}

                    $savestatus[$busno] = $status;
                    $bus[$busno]["stats"]["status"] = $status;
                    if(isset( $bus[ $busno."#" ])) {$bus[$busno."#"]["stats"]["status"] = $status;}
                  }
                }

              } else {
                $fetcherror = true;
                alert("alert",$translation["fetch-error"][$lang]." (".$host.")");
                $savestatus["ERROR"] = "ping";
              } 

              //Output variable to txt
              $var = "";
              foreach ($savestatus as $busname => $status) {
                $var = $var.$busname.",".$status."\n";
              }
              file_put_contents($filename, $var);

          } else {
            foreach(csv_to_array("Data/Status") as $status){
              if($status[0] != "ERROR" ){
                $bus[$status[0]]["stats"]["status"] = $status[1];
                if(isset( $bus[ $status[0]."#" ])) {$bus[$status[0]."#"]["stats"]["status"] = $status[1];}
              } else {
                $fetcherror = true;                
                alert("alert",$translation["fetch-error"][$lang]." (".$host.")");
              }
            }
          }
          

            //Alert Delay Bus
            foreach($bus as $busnum => $busline){
              if($busline["stats"]["status"] == "delay" ) $buserrstat["delay"][] = $busnum;
              if($busline["stats"]["status"] == "suspended" ) $buserrstat["suspended"][] = $busnum;
            }

            $finalerrbus="";
            if(isset($buserrstat["delay"])) $finalerrbus = $finalerrbus.$translation["delay-alert"][$lang].implode(", ",$buserrstat["delay"]);
            if(isset($buserrstat["delay"]) && isset($buserrstat["suspended"])) $finalerrbus = $finalerrbus."<br>";
            if(isset($buserrstat["suspended"])) $finalerrbus = $finalerrbus.$translation["suspended-alert"][$lang].implode(", ",$buserrstat["suspended"]);
            if($finalerrbus !== "") alert("alert", $finalerrbus);
        

            //Concat all bus stops
            foreach ($bus as $busnum) { foreach ($busnum["stations"]["name"] as $busstops) {
                    if (isset($busstops)) {$allbusstop[] = $busstops;}
            }}
            $allbusstop =  array_filter(array_unique($allbusstop));

            //Concat all Buildings
            foreach ($station as $stoparr) { foreach ($stoparr as $buildings) {
                    if (isset($busstops)) {$allbuildings[] = $buildings;}
            }}
            $allbuildings =  array_filter(array_unique($allbuildings));


            //Translate building names
            foreach ($translation as $buildingcode => $buildingnamearr) {
              foreach ($allbuildings as $allbuildingcode){
                if ($buildingcode == $allbuildingcode) {
                  if($buildingnamearr[$lang] == "" || $buildingcode == ""){
                    $transbuilding[] = $buildingnamearr[$lang];
                  } else {
                    $transbuilding[] = $buildingnamearr[$lang]." (".strtoupper($buildingcode).")";
                  }
                }
              }
            }
        ?> 

      <!--Mode Change!-->
        <form name="bussearch" method="post" action="" autocomplete="off">

              <div class="switch-toggle">

                 <!-- <input id="realtime" name="mode" type="radio" value="realtime" <?php if($mode == 2) echo "checked" ?>/>
                  <label for="realtime" onClick="mode_change('realtime');"><?php echo $translation["mode-realtime"][$lang] ?></label> !-->

                  <input id="building" name="mode" type="radio" value="building" <?php if($mode == 1) echo "checked" ?>/>
                  <label for="building" onClick="mode_change('building');"><?php echo $translation["mode-building"][$lang] ?></label>

                  <input id="station" name="mode" type="radio" value="station" <?php if($mode == 0) echo "checked" ?>/>
                  <label for="station" onClick="mode_change('station');"><?php echo $translation["mode-station"][$lang] ?></label>

                  <a></a>
              </div>
      <!--Search Box!-->
      
        <table class="info-table" style="width:100%">
          <tr class="info-bar">
            <td class="info-cell">

              <div class="info-box">

                <?php
                  //Slider (showallroute)
                  if($mode == 0 || $mode == 1){
                    echo '<span style="display: inline-flex;align-items: center; vertical-align: middle;"><label for="showallroute">'.$translation["showallroute-info"][$lang].' </label> <label class="switch">';
                    echo '<input type="checkbox" id="showallroute" name="showallroute"';
                      if( $_POST['showallroute'] == "on") echo "checked";
                    echo '><span class="slider"></span></label></span><br><br>';
                  }


                  //Slider (現在出發)
                  echo '<span style="display: inline-flex;align-items: center; vertical-align: middle;"><label for="deptnow">'.$translation["info-deptnow"][$lang].' </label> <label class="switch">';
                  echo '<input type="checkbox" id="deptnow" name="deptnow" onchange="time_change();"';
                    if( !$fetcherror && ($_POST['deptnow'] == "on" || !isset($_POST['submit']))) echo "checked";
                  echo '><span class="slider"></span></label></span>';
                  

                  //手動時間
                    echo '<br><div id="time-schedule" style="display: none;"><br>';

                    
                      $weekday = array("WK-Sun", "WK-Mon", "WK-Tue", "WK-Wed", "WK-Thu", "WK-Fri", "WK-Sat");
                      echo '<select class="select-date" name="Trav-wk" id="Trav-wk" onchange="date_change();"  style="margin-right: 10px;">';
                        foreach ($weekday as $weekdays => $value) { 
                          echo "<option "; 
                          if(isset($_POST['submit'])){
                            if($value == $_POST['Trav-wk']) echo 'selected="selected"';
                          } else {
                            if(date('N') == $weekdays) echo 'selected="selected"';
                          }
                          echo 'value="'.$value.'">'.$translation[$value][$lang]."</option>"; 
                        }
                      echo "</select> ";


                      //Concat all bus date
                      foreach ($bus as $busnum) { $busdate[] = $busnum["schedule"][3];}
                      $busdate =  array_filter(array_unique($busdate));
                      echo '<select class="select-date" name="Trav-dt" id="Trav-dt" style="margin-right: 10px;"">';
                        foreach ($busdate as $value) { 
                          if(strpos($value,",") == false){
                            echo "<option "; 
                            if(isset($_POST['submit'])){
                              if($value == $_POST['Trav-dt']) echo 'selected="selected"';
                            }
                            echo 'value="'.$value.'">'.$translation[$value][$lang]."</option>"; 
                          }
                        }
                      echo "</select>  ";
                    


                    $hour = array("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
                    echo '<select class="select-time" name="Trav-hr" id="Trav-hr" style="margin-left: 10px;"">';
                      foreach ($hour as $value) { 
                        echo "<option "; 
                        
                        if(isset($_POST['submit'])){
                          if($value == $_POST['Trav-hr']) echo 'selected="selected"';
                        } else {
                          if(date('H') == $value) echo 'selected="selected"';
                        }
                        echo 'value="'.$value.'">'.$value."</option>"; 
                      }
                    echo "</select>  ：  ";


                    $minute = array("00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55");
                    echo '<select class="select-time" name="Trav-min" id="Trav-min">';
                    foreach ($minute as $value) { 
                      echo "<option "; 
                      
                      if(isset($_POST['submit'])){
                        if($value == $_POST['Trav-min']) echo 'selected="selected"';
                      } else {
                        if($value >= date('i') && $value < date('i') + 5) echo 'selected="selected"';
                      }
                      echo 'value="'.$value.'">'.$value."</option>"; 
                    }
                    echo "</select></div>";

                  //自動時間
                  if($mode == 0 || $mode == 1) echo '<div id="time-now" class="show-time" style="display: none;"><br>';
                    if(!$fetcherror){
                      $operating = array();
                      foreach ($bus as $index => $busarr){ if($busarr["stats"]["status"] == "normal" || $busarr["stats"]["status"] == "delay") $operating[] = $index;}
                      if($mode == 0 || $mode == 1){
                        $operating = implode(", ", $operating);
                        if($operating == "") echo $translation["stop-running"][$lang]; else echo $translation["info-running"][$lang].$operating;
                      }
                    } else {
                      if($mode == 0 || $mode == 1) echo $translation["fetch-error"][$lang];
                    }
                    if($mode == 0 || $mode == 1) echo '</div>';

                  //Select Bus
                  if($mode == 2){
                    echo "<br>" .$translation["table-line"][$lang].":  ";
                    echo '<select class="select-date" name="route-realtime" id="route-realtime" style="margin-right: 10px; width: fit-content">';
                      foreach ($operating as $value) { 
                        if(strpos($value, "#") === false){
                          echo "<option "; 
                          if(isset($_POST['submit'])){
                            if($value == $_POST['route-realtime']) echo 'selected="selected"';
                          }
                          echo 'value="'.$value.'">'.$value."</option>"; 
                        }
                      }
                    echo "</select>";
                  
                    //Submit Timeset 
                    echo '<br><br><input id="routesubmitbtn" class="submit-btn" type="submit" name="submit" value="'.$translation["Search-btn"][$lang].'"/>';
                  }
                ?>
              </div>

            </td>

            <?php 
              if($mode == 0 || $mode == 1 )
              echo '<td rowspan="2" id="routechoose" class="info-cell">'.
                '<div class="intro-div">'.
                  '<table width="100%" class="intro-table">'.
                    '<tr>'.
                        '<td>'.
                          '<h3>'.$translation["DescTxt1"][$lang].'</h3>'.
                        '</td>'.
                        '<tr></tr>'.
                          '<td>'.
                              '<table style="width:100%;">'.
                                '<tr style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">'.
                                  '<td height="75px" style="text-align: right ; width: calc((100% - 45%)/2);">'.
                                    '<label for="Start" id="Start-label" style="color: #34495e;">'.$translation["Form-Start"][$lang].'</label>'.
                                  '</td>'.
                                  '<td>';
                                      if($mode == 1){
                                        echo '<div class="autocomplete">'.
                                                '<input style="text-align: center;" class="text-box" type="text" id="Startbd" name="Startbd" autocomplete="off" value="'.$_POST["Startbd"].'">'.
                                              '</div> ';
                                      } else if($mode == 0) {
                                        echo '<select class="select-box" name="Start" id="Start">';
                                          foreach ($allbusstop as $value) { 
                                            echo "<option "; 
                                            if($_POST["Start"] == $value){echo 'selected="selected" ';}; 
                                            echo 'value="'.$value.'">'.$translation[$value][$lang]."</option>"; }
                                        echo "</select>".
                                        "</td><td style='width: calc((100% - 45%)/2);'>";
                                        echo "<img class='image-wrapper' width='100%' src='Images/map.jpg' id='Start-select-box' onclick='map_show_test(0,this.id);'></img>";
                                      }

                                      if($mode == 0 || $mode == 1 )
                                      echo "<img class='image-wrapper' width='100%' src='Images/GPS.jpg' id='Start-GPS-box' onclick='getLocation(this.id);'></img>";

                                if($mode == 0 || $mode == 1 )
                                echo '</td>'.
                                '</tr>'.
                                '<tr style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;">'.
                                  '<td height="75px" style="text-align: right;">'.
                                    '<label for="Dest" id="Dest-label" style="bold; color: #34495e;">'.$translation["Form-Dest"][$lang].'</label>'.
                                  '</td>'.
                                  '<td>';

                                    if($mode == 1){
                                      echo '<div class="autocomplete">'.
                                              '<input style="text-align: center;" class="text-box" type="text" id="Destbd" name="Destbd" autocomplete="off" value="'.$_POST["Destbd"].'">'.
                                            '</div> ';
                                    } else if($mode == 0) {
                                      echo '<select class="select-box" name="Dest" id="Dest">';
                                        foreach ($allbusstop as $value) { 
                                          echo "<option "; 
                                          if($_POST["Dest"] == $value){echo 'selected="selected" ';}; 
                                          echo 'value="'.$value.'">'.$translation[$value][$lang]."</option>"; }
                                      echo "</select>".
                                      "</td><td>";
                                      echo "<img class='image-wrapper' width='100%' src='Images/map.jpg' id='Dest-select-box' onclick='map_show_test(0,this.id);'></img>";
                                    }

                                    if($mode == 0 || $mode == 1 )
                                    echo "<img class='image-wrapper' width='100%' src='Images/GPS.jpg' id='Dest-GPS-box' onclick='getLocation(this.id);'></img>".
                                '</td>'.
                              '</tr>'.
                            '</table>'.
                          '</td>'.
                        '</tr>'.
                        '<tr>'.
                          '<td style="height:40px;">'.
                              '<input id="routesubmitbtn" class="submit-btn" type="submit" name="submit" value="'.$translation["route-submit"][$lang].'"/>'.
                          '</td>'.
                        '</tr>'.
                  '</table>'.
                '</div>'.
              '</td>';
            
            ?>
            
          </tr>
         </table>

      <!--Map Search!-->
          <div id="map-search" class="map-search">
            <table class="img-table" cellspacing="10" style="text-align:center; width: 100%; margin: 0 auto; height: 90vh;">
              <tr class="img-bar">
                <td class="img-cell" style=" height: 30vh; width:300px; vertical-align: bottom;">
                  Credit: <a href="https://t.me/allfilessaveddied">@allfilessaveddied</a>, <a href="https://instagram.com/go_to_cuhk_by_bus">@go_to_cuhk_by_bus</a> 
                </td>
                <td rowspan="3" class="img-cell">
                  <div style="text-align: center">
                    <figure class="zoom" onmousemove="zoom(event,'move')" onmouseleave="zoom(event,'leave')" style='background-image: url("Images/route.jpg");'>
                      <img src="Images/route.jpg" class="map-search-img" usemap="#busmap" id="busmapimg">
                        <map id="mapcood" name="busmap">
                          <area shape="rect" coords="2602, 1296, 2889, 1393" class="photoarea" id="JCPH" onclick="map_area(this.id);" />
                          <area shape="rect" coords="2888, 1518, 3130, 1614" class="photoarea" id="CCEE" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2927, 2199, 3390, 2259" class="photoarea" id="MTR" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2695, 2318, 2870, 2411" class="photoarea" id="MTRP" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2715, 1999, 2836, 2091" class="photoarea" id="YIAP" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2601, 2208, 2905, 2287" class="photoarea" id="CCTEA" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2196, 1786, 2440, 1868" class="photoarea" id="SPORTC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="2405, 1713, 2572, 1784" class="photoarea" id="SHHC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1733, 1717, 2034, 1813" class="photoarea" id="UADM" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1893, 1483, 2032, 1563"  class="photoarea" id="SCIC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1649, 1538, 1869, 1627" class="photoarea" id="SHAWHALL" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="937, 1487, 1179, 1565"  class="photoarea" id="FKHB" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1579, 1371, 1752, 1462" class="photoarea" id="NAC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1451, 1104, 1635, 1198" class="photoarea" id="NAC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1251, 1231, 1426, 1336" class="photoarea" id="UC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="962, 1077, 1170, 1202"  class="photoarea" id="RESI34" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1206, 904, 1412, 1014" class="photoarea" id="CCHH" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1504, 800, 1712, 900" class="photoarea" id="UCSR" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1694, 615, 1881, 709" class="photoarea" id="RESI15" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1873, 425, 2068, 528" class="photoarea" id="RESI10" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1540, 100, 1749, 190" class="photoarea" id="CCEN" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1305, 103, 1484, 211" class="photoarea" id="CWCC" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="1152, 35, 1333, 99" class="photoarea" id="AREA39" onclick="map_area(this.id);"/>
                          <area shape="rect" coords="510, 687, 733, 789" class="photoarea" id="SHAWC" onclick="map_area(this.id);"/>
                        </map>
                      </img>
                    </figure>
                  </div>
                </td>
              </tr>
              <tr class="img-bar">
                <td class="img-cell" style="height: 30vh; width:300px;vertical-align: center;">
                  <h4 id="map-search-heading" style="display:inline; color: black;"></h4>
                  <h4 id="map-search-txt" style="display:inline; color: black;"></h4>
                </td>
              </tr>
              <tr class="img-bar">
                <td class="img-cell" style="height: 30vh; width:300px; vertical-align: top;">
                  <span class="map-submit-btn" onclick='map_show(1,"submit");'>提交</span>
                  <span class="map-submit-btn" onclick='map_show(1,"close");'>取消</span>
                </td>
              </tr>
            </table>           
          </div>
          
        </form>

      <!--GPS Details Box!-->
          <div id="details-box" style="display: none; position: fixed; top: 0; left: 0; height: 100%; width: 100%;">
            <div style="display: table-cell; vertical-align: middle;">               
              <div class="details-box">
                <div style="float:center; height:100px; display: flex; align-items: center;">
                  <h4 id="details-box-heading" style="display:inline; color: black; margin: auto;"><?php echo $translation["nearst_txt"][$lang]?></h4>
                </div>
                <div id="GPSresult" style="overflow:auto;">
                </div>         
              </div>
            </div>
          </div>   

      <!--Route Search!-->
        <?php
        //Var Post items
        runjs(serialize($_POST));
        $postsubmit = $_POST['submit'];
        $poststart = $_POST['Start'];
        $postdest = $_POST['Dest'];
        $postmode = $_POST['mode'];
        $poststartbd = $_POST['Startbd'];
        $postdestbd = $_POST['Destbd'];
        $posttravwk = $_POST['Trav-wk'];
        $posttravdt = $_POST['Trav-dt'];
        $posttravhr = $_POST['Trav-hr'];
        $posttravmin = $_POST['Trav-min'];
        $postdeptnow = $_POST['deptnow'];
        $postshowallroute = $_POST['showallroute'];
        if($postdeptnow == "on")  $posttimebusnum = $_POST['time-busnum-now'];
        else $posttimebusnum = $_POST['time-busnum-manual'];
        if ($postshowallroute == "on") $forceshowexchange = true; else $forceshowexchange = false;
        $poststartbd = strstr($poststartbd, ' (', true) ?: $poststartbd;
        $postdestbd = strstr($postdestbd, ' (', true) ?: $postdestbd;

        
          // Search Bus Lines
          if(isset($postsubmit)){
            if($postdeptnow !== "on"){
              //Translate Time
              if(isset($posttravhr) && isset($posttravmin))
                $currenttime = (DateTime::createFromFormat("H:i", $posttravhr.":".$posttravmin))->getTimestamp();
      
              //Remove Bus Not in Service
              foreach ($bus as $index => $busarr){
                //Base on time
                $starttime = (DateTime::createFromFormat("H:i", $busarr["schedule"][0]))->getTimestamp();
                $endtime = (DateTime::createFromFormat("H:i", $busarr["schedule"][1]))->getTimestamp();
                if($currenttime < $starttime || $currenttime > $endtime) unset($bus[$index]);

                //Base on weekday or day
                if(strpos($busarr["schedule"][3], $posttravdt) === false && $busarr["schedule"][3] !== $posttravdt) unset($bus[$index]);
                if(strpos($busarr["schedule"][4], $posttravwk) === false) unset($bus[$index]);
              
              }
              $bus = array_filter($bus);
            } else {
                foreach ($bus as $index => $busarr){
                  if($busarr["stats"]["status"] == "no" || $busarr["stats"]["status"] == "suspended") unset($bus[$index]);
            }
          }
        if($mode == 0 || $mode == 1){

          //Init
            $startstation = [$poststart];
            $deststation = [$postdest];
            $samestation = false;
            $totalstart = 0;
            $currstart = 0;
            $totaldest = 0;
            $currdest = 0;

            //Check later plz
            if($poststartbd == "" || $postdestbd == "")
              $error = true;
          
          if($postmode == "building"){
            //Building name to Building Code
            foreach (array_reverse($translation) as $buildingcode => $buildingname) {
              foreach ($buildingname as $readablename){
                if($readablename == $poststartbd) $poststartbd = $buildingcode;
                if($readablename == $postdestbd) $postdestbd = $buildingcode;
              }
            }

            //Building to Station
            foreach ($station as $stationcode => $val) {
              foreach ($val as $building){
                if ($building == $poststartbd) {
                  $totalstart = $totalstart + 1;
                  $startstation[] = $stationcode;
                }
                if ($building == $postdestbd) {
                  $totaldest = $totaldest + 1;
                  $deststation[] = $stationcode;
                }
              }
            }

            $startstation = array_values(array_filter(array_unique($startstation)));
            $deststation = array_values(array_filter(array_unique($deststation)));

            //Check Error
            if($totalstart <= 0 || $totaldest <= 0 ){
              echo "<h4>".$translation["building-error"][$lang]."</h4>";
              $error = true;
            }
          }



          do{
            do{
              if($startstation[$currstart] == $deststation[$currdest]) $samestation = true;
              //Startline Bus (Test : 39區 → 善衡書院)
                foreach ($bus as $busno => $line){
                  //Get start station index
                  $successful = 0;
                  $attrline = $line["stations"]["attr"];
                  $timeline = $line["stations"]["time"];
                  $line = $line["stations"]["name"];
                  $busstopnum = array_search($startstation[$currstart], array_reverse($line, true));
                  if($busstopnum !== false){
                    //Get route after start station
                    $searchline = array_slice($line, $busstopnum + 1); 
                    $searchlineattr = array_slice($attrline, $busstopnum); 
                    $searchlinetime = array_slice($timeline, $busstopnum); 

                    //Get end station index
                    $busendstation = array_search($deststation[$currdest], $searchline);
                    if($busendstation !== false ) {
                      $noroute = false;
                      $successful = 1;
                      //Trim $line
                      $newline = array_slice($searchline, 0, $busendstation + 1, true);
                      $newlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                      $newlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                      //Output
                      $routeresult["busno"][] = $busno;
                      foreach($newline as $stopindex => $stop) {
                        $newline[$stopindex] = $translation[$stop][$lang]; 
                      }

                      //Start
                      if($translation[$newlineattr[0]][$lang]) {
                        $startpos = $translation[$startstation[$currstart]][$lang]." (".$translation[$newlineattr[0]][$lang].")";
                      } else {
                        $startpos = $translation[$startstation[$currstart]][$lang];
                      }

                      //End
                      if($translation[end($newlineattr)][$lang]) {
                        $endpos = end($newline)." (".$translation[end($newlineattr)][$lang].")";
                      } else {
                        $endpos = end($newline);
                      }

                      $routeresult["start"][] = $startpos;
                      $routeresult["exchange"][] = "";
                      $routeresult["end"][] = $endpos;
                      $routeresult["time"][] = array_sum($newlinetime);
                      if(isset($bus[$busno."#"]) || strpos($busno, "#") !== false) {
                        $routeresult["route"][] = $startpos." ➤ ".implode(" ➤ ",$newline)."<br><br>".$translation["info-sch"][$lang].$bus[$busno]["schedule"][2];
                      } else {
                        $routeresult["route"][] = $translation[$startstation[$currstart]][$lang]." ➤ ".implode(" ➤ ",$newline);
                      }
                    }

                  if($successful == 0){
                    //try again with ascending
                    $busstopnum = array_search($startstation[$currstart], $line);
                    if($busstopnum !== false){
                      //Get route after start station
                      $searchline = array_slice($line, $busstopnum + 1); 
                      $searchlineattr = array_slice($attrline, $busstopnum); 
                      $searchlinetime = array_slice($timeline, $busstopnum); 
        
                      //Get end station index
                      $busendstation = array_search($deststation[$currdest], $searchline);
                      if($busendstation !== false ) {
                        $noroute = false;
                        //Trim $line
                        $newline = array_slice($searchline, 0, $busendstation + 1, true);
                        $newlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                        $newlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);

                        //Output
                        $routeresult["busno"][] = $busno;
                        foreach($newline as $stopindex => $stop) {$newline[$stopindex] = $translation[$stop][$lang];}

                      //Start
                      if($translation[$newlineattr[0]][$lang]) {
                        $startpos = $translation[$startstation[$currstart]][$lang]." (".$translation[$newlineattr[0]][$lang].")";
                      } else {
                        $startpos = $translation[$startstation[$currstart]][$lang];
                      }
                      
                      //End
                      if($translation[end($newlineattr)][$lang]) {
                        $endpos = end($newline)." (".$translation[end($newlineattr)][$lang].")";
                      } else {
                        $endpos = end($newline);
                      }

                      $routeresult["start"][] = $startpos;
                      $routeresult["exchange"][] = "";
                      $routeresult["end"][] = $endpos;
                      $routeresult["time"][] = array_sum($newlinetime);
                        if(isset($bus[$busno."#"]) || strpos($busno, "#") !== false) {
                          $routeresult["route"][] = $translation[$startstation[$currstart]][$lang]." ➤ ".implode(" ➤ ",$newline)."<br><br>".$translation["info-sch"][$lang].$bus[$busno]["schedule"][2];
                        } else {
                          $routeresult["route"][] = $translation[$startstation[$currstart]][$lang]." ➤ ".implode(" ➤ ",$newline);
                        }
                      }
                    }
                  }
                }
              }

              //No Direct Route (Test : 39區 → 敬文書院 → 地鐵大學站廣場)
              if($noroute || $forceshowexchange){
      
                foreach ($bus as $busno => $line){
                  //Get start station index
                  $attrline = $line["stations"]["attr"];
                  $timeline = $line["stations"]["time"];
                  $line = $line["stations"]["name"];
                  $busstopnum = array_search($startstation[$currstart], $line);
                  if($busstopnum !== false){
                    //Get route after start station
                    $searchline = array_slice($line, $busstopnum + 1); 
                    $searchlineattr = array_slice($attrline, $busstopnum); 
                    $searchlinetime = array_slice($timeline, $busstopnum); 
                    
                    foreach ($searchline as $station){

                      //Let $Station as Start
                      foreach ($bus as $newbusno => $newline){
                        //Get start station index
                        $successful = 0;
                        $newlineattr = $newline["stations"]["attr"];
                        $newlinetime = $newline["stations"]["time"];
                        $newline = $newline["stations"]["name"];
                        $newbusstopnum = array_search($station, array_reverse($newline,true));
                        if($newbusstopnum !== false){
                          //Get route after start station
                          $newsearchline = array_slice($newline, $newbusstopnum + 1); 
                          $newsearchlineattr = array_slice($newlineattr, $newbusstopnum); 
                          $newsearchlinetime = array_slice($newlinetime, $newbusstopnum); 

                          //Get end station index
                          $busendstation = array_search($station, $searchline);
                          $secbusendstation = array_search($deststation[$currdest], $newsearchline);
                          if($secbusendstation !== false && $busendstation !== false) {
                            $successful = 1;
                            $routeresult["busno"][] = $busno."→".$newbusno;

                            //Trim $line & $newsearchline
                            $searchline = array_slice($searchline, 0, $busendstation + 1, true);
                            $newsearchline = array_slice($newsearchline, 0, $secbusendstation + 1, true);
                            $searchlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                            $newsearchlineattr = array_slice($newsearchlineattr, 0, $secbusendstation + 2, true);
                            $searchlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                            $newsearchlinetime = array_slice($newsearchlinetime, 0, $secbusendstation + 2, true);

                            foreach($searchline as $stopindex => $stop) {$searchline[$stopindex] = $translation[$stop][$lang] ; }
                            foreach($newsearchline as $stopindex => $stop) {$newsearchline[$stopindex] = $translation[$stop][$lang] ; }

                            //Start
                            if($translation[$searchlineattr[0]][$lang]) {
                              $startpos = $translation[$startstation[$currstart]][$lang]." (".$translation[$searchlineattr[0]][$lang].")";
                            } else {
                              $startpos = $translation[$startstation[$currstart]][$lang];
                            }

                            //Exchange End
                            if($translation[end($searchlineattr)][$lang]) {
                              $exchangeposen = end($searchline)." (".$translation[end($searchlineattr)][$lang].")";
                            } else {
                              $exchangeposen = end($searchline);
                            }

                            //Exchange Start
                            if($translation[$newsearchlineattr[0]][$lang]) {
                              $exchangeposst = end($searchline)." (".$translation[$newsearchlineattr[0]][$lang].")";
                            } else {
                              $exchangeposst = end($searchline);
                            }

                            //Exchange Group
                            if($exchangeposen == $exchangeposst){
                              $exchangepos = $exchangeposen;
                            } else {
                              $exchangepos = $exchangeposen." → ".$exchangeposst;
                            }

                            //End
                            if($translation[end($newsearchlineattr)][$lang]) {
                              $endpos = end($newsearchline)." (".$translation[end($newsearchlineattr)][$lang].")";
                            } else {
                              $endpos = end($newsearchline);
                            }

                            $routeresult["start"][] = $startpos;
                            $routeresult["exchange"][] = $exchangepos;
                            $routeresult["end"][] = $endpos;
                            $routeresult["time"][] = array_sum($searchlinetime) + array_sum($newsearchlinetime);
                            $routeresult["route"][] = $translation[$startstation[$currstart]][$lang]." ➤ ".implode(" ➤ ",$searchline)." <br> 【 ".$translation["table-transfer"][$lang].$exchangepos." 】 <br> ".$translation[$station][$lang]." ➤ ".implode(" ➤ ",$newsearchline);
                          }

                          if ($successful == 0){
                            //try again with ascending
                            $newbusstopnum = array_search($station, $newline);
                            if($newbusstopnum !== false){
                              //Get route after start station
                              $newsearchline = array_slice($newline, $newbusstopnum + 1); 
                              $newsearchlineattr = array_slice($newlineattr, $newbusstopnum); 
                              $newsearchlinetime = array_slice($newlinetime, $newbusstopnum); 
        
                              //Get end station index
                              $busendstation = array_search($station, $searchline);
                              $secbusendstation = array_search($deststation[$currdest], $newsearchline);
                              
                              if($secbusendstation !== false && $busendstation !== false) {
                                $successful = 1;
                                $routeresult["busno"][] = $busno."→".$newbusno;
        
                                //Trim $line & $newsearchline
                                $searchline = array_slice($searchline, 0, $busendstation + 1, true);
                                $newsearchline = array_slice($newsearchline, 0, $secbusendstation + 1, true);
                                $searchlineattr = array_slice($searchlineattr, 0, $busendstation + 2, true);
                                $newsearchlineattr = array_slice($newsearchlineattr, 0, $secbusendstation + 2, true);
                                $searchlinetime = array_slice($searchlinetime, 0, $busendstation + 2, true);
                                $newsearchlinetime = array_slice($newsearchlinetime, 0, $secbusendstation + 2, true);

                                foreach($searchline as $stopindex => $stop) {$searchline[$stopindex] = $translation[$stop][$lang] ; }
                                foreach($newsearchline as $stopindex => $stop) {$newsearchline[$stopindex] = $translation[$stop][$lang] ; }

                                //Start
                                if($translation[$searchlineattr[0]][$lang]) {
                                  $startpos = $translation[$startstation[$currstart]][$lang]." (".$translation[$searchlineattr[0]][$lang].")";
                                } else {
                                  $startpos = $translation[$startstation[$currstart]][$lang];
                                }

                                //Exchange End
                                if($translation[end($searchlineattr)][$lang]) {
                                  $exchangeposen = end($searchline)." (".$translation[end($searchlineattr)][$lang].")";
                                } else {
                                  $exchangeposen = end($searchline);
                                }

                                //Exchange Start
                                if($translation[$newsearchlineattr[0]][$lang]) {
                                  $exchangeposst = end($searchline)." (".$translation[$newsearchlineattr[0]][$lang].")";
                                } else {
                                  $exchangeposst = end($searchline);
                                }

                                //Exchange Group
                                if($exchangeposen == $exchangeposst){
                                  $exchangepos = $exchangeposen;
                                } else {
                                  $exchangepos = $exchangeposen." → ".$exchangeposst;
                                }

                                //End
                                if($translation[end($newsearchlineattr)][$lang]) {
                                  $endpos = end($newsearchline)." (".$translation[end($newsearchlineattr)][$lang].")";
                                } else {
                                  $endpos = end($newsearchline);
                                }

                                $routeresult["start"][] = $startpos;
                                $routeresult["exchange"][] = $exchangepos;
                                $routeresult["end"][] = $endpos;
                                $routeresult["time"][] = array_sum($searchlinetime) + array_sum($newsearchlinetime);
                                $routeresult["route"][] = $translation[$startstation[$currstart]][$lang]." ➤ ".implode(" ➤ ",$searchline)." <br> 【 ".$translation["table-transfer"][$lang].$translation[$station][$lang]." 】 <br> ".$translation[$station][$lang]." ➤ ".implode(" ➤ ",$newsearchline);
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
              $currstart = $currstart + 1;
            } while( $currstart < $totalstart );
            $currstart = 0;
            $currdest = $currdest + 1;
          } while( $currdest < $totaldest );


            if(empty($routeresult)){ 
              $routeresult["busno"] = array("N/A");  
              $routeresult["route"] = array($translation["No-BUS"][$lang]);
              $noroute = 1;
            }
        
            //Group Result
            foreach ($routeresult["busno"] as $index => $startloc){

              //time process
              ($routeresult["time"][$index] == 0)? $timeoutput = "N/A" : $timeoutput = round($routeresult["time"][$index]/60);

              $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["busno"][] = $routeresult["busno"][$index];
              $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["route"][] = $routeresult["route"][$index];
              $routegroupresult[$routeresult["start"][$index]][$routeresult["end"][$index]][$routeresult["exchange"][$index]]["timeused"][] = $timeoutput;
            }
          } 
        }

        if($mode==2 && isset($postsubmit)) {
          //set time
          if ($postdeptnow == "on") $searchtime = strtotime(DateTime::createFromFormat('H:i', date('H:i'))->format('Y-m-d H:i:s'));
          else $searchtime = strtotime(DateTime::createFromFormat('H:i',$posttravhr.':'.$posttravmin)->format('Y-m-d H:i:s'));


          foreach ($bus as $busno => $busdetails){
            foreach ($busdetails["stations"]["arrivaltime"] as $stationindex => $stations ){
              if($translation[$busdetails["stations"]["attr"][$stationindex]][$lang])
                $stationname = $translation[$busdetails["stations"]["name"][$stationindex]][$lang]." (".$translation[$busdetails["stations"]["attr"][$stationindex]][$lang].")";
              else 
              $stationname = $translation[$busdetails["stations"]["name"][$stationindex]][$lang];
              foreach ($stations as $arrivaltime) {
                $displaytime = ($arrivaltime - $searchtime) / 60;
                if ($displaytime < 2 ) {
                  $displaytimetext = $translation["time-arriving"][$lang];
                  if ($displaytime < -0.5 ) $displaytimetext = $translation["time-missed"][$lang];
                } else {
                  $displaytimetext = round($displaytime);
                }
                if((strval($posttimebusnum) === strval($busno) || strval($posttimebusnum."#") === strval($busno)) && $arrivaltime >= $searchtime - 300 && $arrivaltime <= $searchtime + 1800)
                  if (in_array($busno, array_keys($bus)))
                    $busarrivaltime[] = array($busno,$stationname,date('h:i A',$arrivaltime),$displaytimetext,$stationindex);
              }
            }
          }
          if($busarrivaltime)
            usort($busarrivaltime, function($a, $b) {return $a[1] <=> $b[1];});
        }
        ?> 


      <!--Route Result!-->
        <?php
        if(isset($postsubmit) && !$error && $mode !== 2){
          echo '<table id="routeresult" cellspacing="1" cellpadding="10" style="margin: 50px auto; width:90%; font-size:max(16px, 2vmin); font-family: sans-serif; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">'.
                '<tr style="background-color: #009879; color: #ffffff; text-align: center;">'.
                  '<td style="width: 7vmin">'.$translation["table-line"][$lang].'</td>'.
                  '<td>'.$translation["table-route"][$lang].'</td>'.
                  '<td style="width: 14vmin">'.$translation["table-detals"][$lang].'</td>'.
                '</tr>';

          if($samestation) {
            echo '<tr style="background-color: red; color: #ecf0f1; text-align: center;">'.
                    '<td colspan="3" style="width: 7vmin">'.$translation["samestation-info"][$lang].'</td>'.
                  '</tr>';
          }
        
          foreach ($routegroupresult as $start => $temp ) {
            foreach ($temp as $end => $temp ) {
              foreach ($temp as $exchange => $busroutes) {
                if($start !== ""){
                echo '<tr style="background-color: #c7ecee;"><td colspan="3" style="padding-left:15px;">';
                  if ($exchange === "") { echo $start." → ".$end;}
                  else { echo $start." → ".$exchange." → ".$end;}
                echo '</td></tr>';
                }

                foreach ($busroutes["busno"] as $index => $busno)
                echo '<tr style="background-color: #ecf0f1;">'.
                '<td style="text-align:center;">'.$busroutes["busno"][$index].'</td>'.
                '<td>'.$busroutes["route"][$index].'</td>'.
                '<td style="text-align:center;">'.
                  $translation["time-heading-arriving"][$lang].$busroutes["timeused"][$index]." min<br>".
                  //$translation["time-heading-arriving"][$lang].$busroutes["waittime"][$index]." min<br>";
                '</td>'.
                '</tr> ';
              }
            }
          }
        }
        else if($mode == 2 && isset($postsubmit)) {
          echo '<table id="routeresult" cellspacing="1" cellpadding="10" style="margin: 50px auto; width:90%; font-size:max(16px, 2vmin); font-family: sans-serif; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">'.
          '<tr style="background-color: #009879; color: #ffffff; text-align: center;">'.
            '<td style="width: 7vmin">'.$translation["table-line"][$lang].'</td>'.
            //'<td>'."Station".'</td>'.
            '<td style="width: 14vmin">'.$translation["table-detals"][$lang].'</td>'.
            //'<td style="width: 14vmin">'."Time Left".'</td>'.
          '</tr>';

          if($busarrivaltime){
            foreach ($busarrivaltime as $busdetails){
              if( $lastbusstop !== $busdetails[1])
                echo '<tr style="background-color: #c7ecee;"><td colspan="3" style="padding-left:15px;">'.$busdetails[1].'</td></tr>';

              echo "<tr>".
                '<td style="text-align:center;">'.$busdetails[0].'</td>'.
                //'<td style="text-align:center;">'.$busdetails[2]."</td>".
                '<td style="text-align:center;">'.$busdetails[3]."</td>".
              "</tr>";
              $lastbusstop = $busdetails[1];
            }
          } else {
            echo "<tr>".
            '<td style="text-align:center;" colspan="3">'.$translation["No-bus-time"][$lang].'</td>'.
          "</tr>";
          }
        }
         //echo '<tr style="background-color: #c7ecee;">'.'<td style="text-align:center;">'.$busno.'</td>'.'<td>'.$routegroupresult["route"][$index].'</td>'.'<!--<td style="text-align:center;">'.$translation["New-Feature"][$lang].'</td>!-->'.'</tr> ';
        ?>
        </table>

      <!--Notes!-->
        <br>
        <h2><a style="color: #685206; text-decoration: none;" href="https://forms.gle/g4xDpa5oEVqzHjFt7g"><?php echo $translation["Update-Request"][$lang] ?></a></h2>
        <h2><a style="color: #685206; text-decoration: none;" href="https://github.com/AnsonCheng03"><?php echo $translation["author-notes"][$lang] ?>@AnsonCheng03</a></h2>
        <hr>
        <h2 style="color: #685206; text-decoration: none;"><?php echo $translation["credit-notes"][$lang] ?></h2>
        <hr>
        <h2 style="color: #685206; text-decoration: none;"><?php echo $translation["buy-coffee"][$lang] ?></h2>
        <br>
      <!--Ads!-->
        <table style="margin-bottom:100px;" width="100%">
          <tr>
            <td style="text-align:center">
            <br>
            <div class="bmc-btn-container">
              <a class="bmc-btn" target="_blank" href="http://buymeacoffee.com/cubus">
                <svg viewBox="0 0 884 1279" fill="none">
                  <path d="M791.109 297.518L790.231 297.002L788.201 296.383C789.018 297.072 790.04 297.472 791.109 297.518Z" fill="#0D0C22"></path>
                  <path d="M803.896 388.891L802.916 389.166L803.896 388.891Z" fill="#0D0C22"></path>
                  <path d="M791.484 297.377C791.359 297.361 791.237 297.332 791.118 297.29C791.111 297.371 791.111 297.453 791.118 297.534C791.252 297.516 791.379 297.462 791.484 297.377Z" fill="#0D0C22"></path>
                  <path d="M791.113 297.529H791.244V297.447L791.113 297.529Z" fill="#0D0C22"></path>
                  <path d="M803.111 388.726L804.591 387.883L805.142 387.573L805.641 387.04C804.702 387.444 803.846 388.016 803.111 388.726Z" fill="#0D0C22"></path>
                  <path d="M793.669 299.515L792.223 298.138L791.243 297.605C791.77 298.535 792.641 299.221 793.669 299.515Z" fill="#0D0C22"></path>
                  <path d="M430.019 1186.18C428.864 1186.68 427.852 1187.46 427.076 1188.45L427.988 1187.87C428.608 1187.3 429.485 1186.63 430.019 1186.18Z" fill="#0D0C22"></path>
                  <path d="M641.187 1144.63C641.187 1143.33 640.551 1143.57 640.705 1148.21C640.705 1147.84 640.86 1147.46 640.929 1147.1C641.015 1146.27 641.084 1145.46 641.187 1144.63Z" fill="#0D0C22"></path>
                  <path d="M619.284 1186.18C618.129 1186.68 617.118 1187.46 616.342 1188.45L617.254 1187.87C617.873 1187.3 618.751 1186.63 619.284 1186.18Z" fill="#0D0C22"></path>
                  <path d="M281.304 1196.06C280.427 1195.3 279.354 1194.8 278.207 1194.61C279.136 1195.06 280.065 1195.51 280.684 1195.85L281.304 1196.06Z" fill="#0D0C22"></path>
                  <path d="M247.841 1164.01C247.704 1162.66 247.288 1161.35 246.619 1160.16C247.093 1161.39 247.489 1162.66 247.806 1163.94L247.841 1164.01Z" fill="#0D0C22"></path>
                  <path class="logo-coffee" d="M472.623 590.836C426.682 610.503 374.546 632.802 306.976 632.802C278.71 632.746 250.58 628.868 223.353 621.274L270.086 1101.08C271.74 1121.13 280.876 1139.83 295.679 1153.46C310.482 1167.09 329.87 1174.65 349.992 1174.65C349.992 1174.65 416.254 1178.09 438.365 1178.09C462.161 1178.09 533.516 1174.65 533.516 1174.65C553.636 1174.65 573.019 1167.08 587.819 1153.45C602.619 1139.82 611.752 1121.13 613.406 1101.08L663.459 570.876C641.091 563.237 618.516 558.161 593.068 558.161C549.054 558.144 513.591 573.303 472.623 590.836Z" fill="#FFDD00"></path>
                  <path d="M78.6885 386.132L79.4799 386.872L79.9962 387.182C79.5987 386.787 79.1603 386.435 78.6885 386.132Z" fill="#0D0C22"></path>
                  <path class="logo-outline" d="M879.567 341.849L872.53 306.352C866.215 274.503 851.882 244.409 819.19 232.898C808.711 229.215 796.821 227.633 788.786 220.01C780.751 212.388 778.376 200.55 776.518 189.572C773.076 169.423 769.842 149.257 766.314 129.143C763.269 111.85 760.86 92.4243 752.928 76.56C742.604 55.2584 721.182 42.8009 699.88 34.559C688.965 30.4844 677.826 27.0375 666.517 24.2352C613.297 10.1947 557.342 5.03277 502.591 2.09047C436.875 -1.53577 370.983 -0.443234 305.422 5.35968C256.625 9.79894 205.229 15.1674 158.858 32.0469C141.91 38.224 124.445 45.6399 111.558 58.7341C95.7448 74.8221 90.5829 99.7026 102.128 119.765C110.336 134.012 124.239 144.078 138.985 150.737C158.192 159.317 178.251 165.846 198.829 170.215C256.126 182.879 315.471 187.851 374.007 189.968C438.887 192.586 503.87 190.464 568.44 183.618C584.408 181.863 600.347 179.758 616.257 177.304C634.995 174.43 647.022 149.928 641.499 132.859C634.891 112.453 617.134 104.538 597.055 107.618C594.095 108.082 591.153 108.512 588.193 108.942L586.06 109.252C579.257 110.113 572.455 110.915 565.653 111.661C551.601 113.175 537.515 114.414 523.394 115.378C491.768 117.58 460.057 118.595 428.363 118.647C397.219 118.647 366.058 117.769 334.983 115.722C320.805 114.793 306.661 113.611 292.552 112.177C286.134 111.506 279.733 110.801 273.333 110.009L267.241 109.235L265.917 109.046L259.602 108.134C246.697 106.189 233.792 103.953 221.025 101.251C219.737 100.965 218.584 100.249 217.758 99.2193C216.932 98.1901 216.482 96.9099 216.482 95.5903C216.482 94.2706 216.932 92.9904 217.758 91.9612C218.584 90.9319 219.737 90.2152 221.025 89.9293H221.266C232.33 87.5721 243.479 85.5589 254.663 83.8038C258.392 83.2188 262.131 82.6453 265.882 82.0832H265.985C272.988 81.6186 280.026 80.3625 286.994 79.5366C347.624 73.2301 408.614 71.0801 469.538 73.1014C499.115 73.9618 528.676 75.6996 558.116 78.6935C564.448 79.3474 570.746 80.0357 577.043 80.8099C579.452 81.1025 581.878 81.4465 584.305 81.7391L589.191 82.4445C603.438 84.5667 617.61 87.1419 631.708 90.1703C652.597 94.7128 679.422 96.1925 688.713 119.077C691.673 126.338 693.015 134.408 694.649 142.03L696.732 151.752C696.786 151.926 696.826 152.105 696.852 152.285C701.773 175.227 706.7 198.169 711.632 221.111C711.994 222.806 712.002 224.557 711.657 226.255C711.312 227.954 710.621 229.562 709.626 230.982C708.632 232.401 707.355 233.6 705.877 234.504C704.398 235.408 702.75 235.997 701.033 236.236H700.895L697.884 236.649L694.908 237.044C685.478 238.272 676.038 239.419 666.586 240.486C647.968 242.608 629.322 244.443 610.648 245.992C573.539 249.077 536.356 251.102 499.098 252.066C480.114 252.57 461.135 252.806 442.162 252.771C366.643 252.712 291.189 248.322 216.173 239.625C208.051 238.662 199.93 237.629 191.808 236.58C198.106 237.389 187.231 235.96 185.029 235.651C179.867 234.928 174.705 234.177 169.543 233.397C152.216 230.798 134.993 227.598 117.7 224.793C96.7944 221.352 76.8005 223.073 57.8906 233.397C42.3685 241.891 29.8055 254.916 21.8776 270.735C13.7217 287.597 11.2956 305.956 7.64786 324.075C4.00009 342.193 -1.67805 361.688 0.472751 380.288C5.10128 420.431 33.165 453.054 73.5313 460.35C111.506 467.232 149.687 472.807 187.971 477.556C338.361 495.975 490.294 498.178 641.155 484.129C653.44 482.982 665.708 481.732 677.959 480.378C681.786 479.958 685.658 480.398 689.292 481.668C692.926 482.938 696.23 485.005 698.962 487.717C701.694 490.429 703.784 493.718 705.08 497.342C706.377 500.967 706.846 504.836 706.453 508.665L702.633 545.797C694.936 620.828 687.239 695.854 679.542 770.874C671.513 849.657 663.431 928.434 655.298 1007.2C653.004 1029.39 650.71 1051.57 648.416 1073.74C646.213 1095.58 645.904 1118.1 641.757 1139.68C635.218 1173.61 612.248 1194.45 578.73 1202.07C548.022 1209.06 516.652 1212.73 485.161 1213.01C450.249 1213.2 415.355 1211.65 380.443 1211.84C343.173 1212.05 297.525 1208.61 268.756 1180.87C243.479 1156.51 239.986 1118.36 236.545 1085.37C231.957 1041.7 227.409 998.039 222.9 954.381L197.607 711.615L181.244 554.538C180.968 551.94 180.693 549.376 180.435 546.76C178.473 528.023 165.207 509.681 144.301 510.627C126.407 511.418 106.069 526.629 108.168 546.76L120.298 663.214L145.385 904.104C152.532 972.528 159.661 1040.96 166.773 1109.41C168.15 1122.52 169.44 1135.67 170.885 1148.78C178.749 1220.43 233.465 1259.04 301.224 1269.91C340.799 1276.28 381.337 1277.59 421.497 1278.24C472.979 1279.07 524.977 1281.05 575.615 1271.72C650.653 1257.95 706.952 1207.85 714.987 1130.13C717.282 1107.69 719.576 1085.25 721.87 1062.8C729.498 988.559 737.115 914.313 744.72 840.061L769.601 597.451L781.009 486.263C781.577 480.749 783.905 475.565 787.649 471.478C791.392 467.391 796.352 464.617 801.794 463.567C823.25 459.386 843.761 452.245 859.023 435.916C883.318 409.918 888.153 376.021 879.567 341.849ZM72.4301 365.835C72.757 365.68 72.1548 368.484 71.8967 369.792C71.8451 367.813 71.9483 366.058 72.4301 365.835ZM74.5121 381.94C74.6842 381.819 75.2003 382.508 75.7337 383.334C74.925 382.576 74.4089 382.009 74.4949 381.94H74.5121ZM76.5597 384.641C77.2996 385.897 77.6953 386.689 76.5597 384.641V384.641ZM80.672 387.979H80.7752C80.7752 388.1 80.9645 388.22 81.0333 388.341C80.9192 388.208 80.7925 388.087 80.6548 387.979H80.672ZM800.796 382.989C793.088 390.319 781.473 393.726 769.996 395.43C641.292 414.529 510.713 424.199 380.597 419.932C287.476 416.749 195.336 406.407 103.144 393.382C94.1102 392.109 84.3197 390.457 78.1082 383.798C66.4078 371.237 72.1548 345.944 75.2003 330.768C77.9878 316.865 83.3218 298.334 99.8572 296.355C125.667 293.327 155.64 304.218 181.175 308.09C211.917 312.781 242.774 316.538 273.745 319.36C405.925 331.405 540.325 329.529 671.92 311.91C695.906 308.686 719.805 304.941 743.619 300.674C764.835 296.871 788.356 289.731 801.175 311.703C809.967 326.673 811.137 346.701 809.778 363.615C809.359 370.984 806.139 377.915 800.779 382.989H800.796Z" fill="#0D0C22"></path>
                </svg>
                <span class="bmc-btn-text">
                  <?php echo $translation["Support-btn"][$lang]; ?>
                </span>
              </a>
            </div>
        </table>

      <!--Add to Homescreen Prompt!-->
        <div id="HomeScreenPrompt" style="z-index:9999; background-color: #ffeaa7; color:#62529c; bottom: 0; left: 0; margin: 0 8px 10px; overflow: hidden; position: fixed; width: calc(100% - 16px); display: none; border-radius:15px">
          <div style="display: flex; justify-content: space-between; padding: 13px 30px 0px 30px;">
            <p><b><?php echo $translation["addhomeios-heading"][$lang]?></b></p>
            <button style="color:#62529c; padding: 0; border: 0; background: transparent; cursor: pointer;" onclick='document.getElementById("HomeScreenPrompt").style.display = "none"; localStorage.setItem("dismisshomescreen", new Date());'><b><?php echo $translation["cancel_btntxt"][$lang]?></b></button>
          </div>
          <div style="margin: 0 16px; padding: 0px 16px 16px 16px;">
            <div style="align-items: center; display: flex; flex-flow: row nowrap; ">
              <svg viewBox="0 0 120 169" style="height: 30px; margin-right: 32px; width: 25px;">
                <g fill="currentColor">
                  <path d="M60 0l28 28-2 2a586 586 0 0 0-4 4L64 15v90h-8V15L38 34l-4-4-2-2L60 0z"></path>
                  <path d="M0 49h44v8H8v104h104V57H76v-8h44v120H0V49z"></path>
                </g>
              </svg>
              <p><?php echo $translation["addhomeios-text1"][$lang]?></p>
            </div>
            <div style="align-items: center; display: flex; flex-flow: row nowrap; ">
              <svg viewBox="55.99425507 31.98999977 157.76574707 157.76371765" style="height: 30px; margin-right: 32px; width: 25px;">
                <path fill="#62529c" d="M90.49 32.83a54.6 54.6 0 019.55-.84c23.98.03 47.96 0 71.94.01 8.5.07 17.3 1.74 24.4 6.65 10.94 7.28 16.52 20.54 17.35 33.3.06 26.03 0 52.06.03 78.08 0 10.16-3.59 20.56-10.95 27.73-7.93 7.61-18.94 11.43-29.79 11.98-25.71.03-51.42 0-77.12.01-10.37-.11-21.01-3.77-28.17-11.48-8.22-8.9-11.72-21.29-11.73-33.21.01-23.03-.03-46.05.02-69.07-.01-9.14 1.33-18.71 6.65-26.4 6.21-9.4 16.97-14.79 27.82-16.76m38.18 41.09c-.05 10.25.01 20.5 0 30.75-9.58-.03-19.16.02-28.75-.04-2.27.08-4.98-.25-6.68 1.61-2.84 2.34-2.75 7.12.01 9.48 1.8 1.69 4.46 1.57 6.75 1.64 9.56-.04 19.12-.01 28.67-.03.02 10.24-.06 20.48.01 30.72-.14 2.66 1.36 5.4 3.95 6.3 3.66 1.66 8.52-1.13 8.61-5.23.26-10.59.02-21.2.09-31.79 9.88 0 19.76.02 29.64.01 2.74.12 5.85-.67 7.14-3.34 2.23-3.75-.61-9.34-5.08-9.29-10.57-.14-21.14-.01-31.7-.04-.01-10.25.04-20.49 0-30.74.3-3.5-2.66-7.09-6.3-6.79-3.65-.33-6.66 3.26-6.36 6.78z"></path>
                <path fill="transparent" d="M128.67 73.92c-.3-3.52 2.71-7.11 6.36-6.78 3.64-.3 6.6 3.29 6.3 6.79.04 10.25-.01 20.49 0 30.74 10.56.03 21.13-.1 31.7.04 4.47-.05 7.31 5.54 5.08 9.29-1.29 2.67-4.4 3.46-7.14 3.34-9.88.01-19.76-.01-29.64-.01-.07 10.59.17 21.2-.09 31.79-.09 4.1-4.95 6.89-8.61 5.23-2.59-.9-4.09-3.64-3.95-6.3-.07-10.24.01-20.48-.01-30.72-9.55.02-19.11-.01-28.67.03-2.29-.07-4.95.05-6.75-1.64-2.76-2.36-2.85-7.14-.01-9.48 1.7-1.86 4.41-1.53 6.68-1.61 9.59.06 19.17.01 28.75.04.01-10.25-.05-20.5 0-30.75z"></path>
              </svg>
              <p><?php echo $translation["addhomeios-text2"][$lang]?></p>
            </div>
          </div>
        </div>


      <!--Script!-->
        <script>
          //Auto Suggestion
          var choices = ["<?php echo implode('","',$transbuilding); ?>"];
          if(document.getElementById("Startbd") && document.getElementById("Destbd")){
            autocomplete(document.getElementById("Startbd"), choices);
            autocomplete(document.getElementById("Destbd"), choices);
          }
        </script>
        <script type="text/javascript" src="Essential/component.js?v=<?php echo $version?>"></script>

    </body>
</html>
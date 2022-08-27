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
  <link rel="apple-touch-icon" href="Images/bus.ico" />
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
  <link rel="stylesheet" href="Essential/mainpage.css?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>">
  <link rel="stylesheet" href="Essential/component.css?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>">
  <script src="Essential/mainpage.js?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3579541618707661" crossorigin="anonymous"></script>
</head>

<!--Functions, Init, Data-->
 <?php
  //Init Program
  include_once('Essential/functions/functions.php');
  date_default_timezone_set("Asia/Hong_Kong");
  $fetcherror = false;
  $lang = urlquery("lang") == "en" ? 1 : 0;
  include('Essential/functions/initdatas.php'); //Download datas from server
  ?>

  <!--Select Language && Function Buttons-->
  <div class="lang-selector nav">
    <button onclick="append_query('lang','tc');" />中文</button>
    <button onclick="append_query('lang','en');" />ENG</button>
  </div>

  <div class="refreshbtn nav">
    <button id="refresh-btn" style="display: none;" onclick="window.location.reload();" /><?php echo $translation["refresh-btn"][$lang] ?></button>
  </div>


  <!--Title & Alert-->
  <h1><?php echo $translation["WEB-Title"][$lang] ?></h1>
  <h2>v. <?php echo $version . "<br> ( " . $translation["last-update"][$lang] . date("d/m/Y H:i", filemtime("Data/Station.csv")) . " )" ?></h2>

  <?php
  foreach ($notice as $noti) {
    alert($noti["pref"]["type"], $noti["content"][$lang]);
  }
  ?>

  <!--Fetch Busstop && Buildings to Array!-->
  <!--Need Fix!-->
  <?php

  $busservices = json_decode(file_get_contents('Data/Status.json'), true);
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


  //Alert Delay Bus
  foreach ($bus as $busnum => $busline) {
    if ($busline["stats"]["prevstatus"] == "normal" && $busline["stats"]["status"] == "no") $buserrstat["justeos"][] = $busnum;
    if ($busline["stats"]["status"] == "delay") $buserrstat["delay"][] = $busnum;
    if ($busline["stats"]["status"] == "suspended") $buserrstat["suspended"][] = $busnum;
  }

  $finalerrbus = "";
  if (isset($buserrstat["delay"])) $finalerrbus = $finalerrbus . $translation["delay-alert"][$lang] . implode(", ", $buserrstat["delay"]);
  if (isset($buserrstat["delay"]) && isset($buserrstat["suspended"])) $finalerrbus = $finalerrbus . "<br>";
  if (isset($buserrstat["suspended"])) $finalerrbus = $finalerrbus . $translation["suspended-alert"][$lang] . implode(", ", $buserrstat["suspended"]);
  if ($finalerrbus !== "") alert("alert", $finalerrbus);

  if (isset($buserrstat["justeos"])) alert("info", $translation["justeos-alert"][$lang] . implode(", ", $buserrstat["justeos"]));

  //Concat all bus stops
  foreach ($bus as $busnum) {
    foreach ($busnum["stations"]["name"] as $busstops) {
      if (isset($busstops)) {
        $allbusstop[] = $busstops;
      }
    }
  }
  $allbusstop =  array_filter(array_unique($allbusstop));

  //Concat all Buildings
  foreach ($station as $stoparr) {
    foreach ($stoparr as $buildings) {
      if (isset($busstops)) {
        $allbuildings[] = $buildings;
      }
    }
  }
  $allbuildings =  array_filter(array_unique($allbuildings));


  //Translate building names
  foreach ($translation as $buildingcode => $buildingnamearr) {
    foreach ($allbuildings as $allbuildingcode) {
      if ($buildingcode == $allbuildingcode) {
        if ($buildingnamearr[$lang] == "" || $buildingcode == "") {
          $transbuilding[] = $buildingnamearr[$lang];
        } else {
          $transbuilding[] = $buildingnamearr[$lang] . " (" . strtoupper($buildingcode) . ")";
        }
      }
    }
  }
  ?>

  <!--Input form!-->
  <form name="bussearch" method="post" onsubmit="return submitform(this,'.routeresult','routesearch/index.php')" autocomplete="off">
    <input hidden type="hidden" name="language" value="<?php echo $lang ?>"></input>

    <div class="switch-toggle">
      <input id="building" name="mode" type="radio" value="building" checked />
      <label for="building"><?php echo $translation["mode-building"][$lang] ?></label>

      <input id="station" name="mode" type="radio" value="station" />
      <label for="station"><?php echo $translation["mode-station"][$lang] ?></label>

      <input disabled id="realtime" name="mode0" type="radio" value="realtime" onclick="append_query('mode', 'realtime');"/>
      <label for="realtime" onclick="append_query('mode', 'realtime');"><?php echo $translation["mode-realtime"][$lang] ?></label>
    </div>

    <div class="search-boxes">
      <div class="info-box optionssel">

        <div class="bus-options">
          <span class="slider-wrapper">
            <label for="showallroute"><?php echo $translation["showallroute-info"][$lang] ?></label>
            <label class="switch"><input type="checkbox" id="showallroute" name="showallroute">
              <span class="slider"></span>
            </label>
          </span>
        </div>

        <div class="bus-options">
          <span class="slider-wrapper">
            <label for="deptnow"><?php echo $translation["info-deptnow"][$lang] ?></label>
            <label class="switch"><input type="checkbox" id="deptnow" name="deptnow" checked onchange="time_change();">
              <span class="slider"></span>
            </label>
          </span>
        </div>



        <!--手動時間!-->
        <div id="time-schedule" style="display: none;">'
          <select class="select-date" name="Trav-wk" id="Trav-wk" onchange="date_change();">
            <?php
            $weekday = ["WK-Sun", "WK-Mon", "WK-Tue", "WK-Wed", "WK-Thu", "WK-Fri", "WK-Sat"];
            foreach ($weekday as $weekdays => $value)
              echo '<option ' . (date('N') == $weekdays ? 'selected ' : '') . 'value="' . $value . '" >' . $translation[$value][$lang] . "</option>";
            ?>
          </select>

          <select class="select-date" name="Trav-dt" id="Trav-dt">
            <?php
            $busdate = array_filter(array_unique(array_column(array_column($bus, 'schedule'), 3)));
            foreach ($busdate as $value)
              if (strpos($value, ",") == false)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
            ?>
          </select>

          <select class="select-time" name="Trav-hr" id="Trav-hr">
            <?php
            $hour = array("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
            foreach ($hour as $value) {
              echo "<option " . (date('H') == $value ? 'selected ' : '') . 'value="' . $value . '">' . $value . "</option>";
            }
            ?>
          </select>
          :
          <select class="select-time" name="Trav-min" id="Trav-min">
            <?php
            $minute = array("00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55");
            foreach ($minute as $value) {
              echo "<option " . ($value >= date('i') && $value < date('i') + 5 ? ' selected ' : '') . 'value="' . $value . '">' . $value . "</option>";
            }
            ?>
          </select>
        </div>

        <!--自動時間!-->
        <div id="time-now" class="show-time" style="display: none;">
          <?php
          if ($fetcherror)
            echo $translation["fetch-error"][$lang];
          else {
            $operating = array_keys(array_filter($bus, function ($busarr) {
              return $busarr["stats"]["status"] == "normal" || $busarr["stats"]["status"] == "delay";
            }));

            if ($operating)
              echo $translation["info-running"][$lang] . implode(", ", $operating);
            else
              echo $translation["stop-running"][$lang];
          }
          ?>
        </div>
      </div>


      <div class="info-box routesel">
        <h3><?php echo $translation["DescTxt1"][$lang] ?> </h3>


        <div class="locationchooser">
          <label for="Start" id="Start-label"><?php echo $translation["Form-Start"][$lang] ?></label>
          <div class="locationinput">
            <div mode="building" class="autocomplete">
              <input style="text-align: center;" class="text-box" type="text" id="Startbd" name="Startbd" autocomplete="off">
            </div>
            <select mode="station" class="select-box" name="Start" id="Start">
              <?php
              foreach ($allbusstop as $value)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
              ?>
            </select>
          </div>
          <div class="functionbuttons">
            <img class='image-wrapper' src='Images/map.jpg' id='Start-select-box' onclick='map_show_test(0,this.id);'></img>
            <img class='image-wrapper' src='Images/GPS.jpg' id='Start-GPS-box' onclick='getLocation(this.id);'></img>
          </div>
        </div>

        <div class="locationchooser">
          <label for="Dest" id="Dest-label"> <?php echo $translation["Form-Dest"][$lang] ?></label>
          <div class="locationinput">
            <div mode="building" class="autocomplete">
              <input style="text-align: center;" class="text-box" type="text" id="Destbd" name="Destbd" autocomplete="off">
            </div>
            <select mode="station" class="select-box" name="Dest" id="Dest">
              <?php
              foreach ($allbusstop as $value)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
              ?>
            </select>
          </div>
          <div class="functionbuttons">
            <img class='image-wrapper' src='Images/map.jpg' id='Dest-select-box' onclick='map_show_test(0,this.id);'></img>
            <img class="image-wrapper" src="Images/GPS.jpg" id="Dest-GPS-box" onclick="getLocation(this.id);"></img>
          </div>
        </div>

        <input id="routesubmitbtn" class="submit-btn" type="submit" name="submit" value=" <?php echo $translation["route-submit"][$lang] ?> " />
      </div>
    </div>
  </form>

  <!--GPS Details Box!-->
  <div id="details-box">
    <div class="details-box">
      <div class="showdetails">
        <h4 id="details-box-heading">
          <?php echo $translation["nearst_txt"][$lang] ?>
        </h4>
      </div>
      <div id="GPSresult"></div>
    </div>
  </div>
  <!--Output result!-->
  <div class="routeresult"></div>




  <!--Notes!-->
  <br>
  <h2><a style="color: #685206; text-decoration: none;" href="https://forms.gle/g4xDpa5oEVqzHjFt7"><?php echo $translation["Update-Request"][$lang] ?></a></h2>
  <h2><a style="color: #685206; text-decoration: none;" href="https://github.com/AnsonCheng03"><?php echo $translation["author-notes"][$lang] ?>@AnsonCheng03</a></h2>
  <hr>
  <h2 style="color: #685206; text-decoration: none;"><?php echo $translation["credit-notes"][$lang] ?></h2>
  <hr>
  <h2 style="color: #685206; text-decoration: none;"><?php echo $translation["buy-coffee"][$lang] ?></h2>
  <br>

  <!--Ads!-->
  <a class="bmc-btn" target="_blank" href="https://payme.hsbc/anson03">
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

  <!--Add to Homescreen Prompt!-->
  <div id="HomeScreenPrompt">
    <div class="desc">
      <p><b><?php echo $translation["addhomeios-heading"][$lang] ?></b></p>
      <button onclick=' document.getElementById("HomeScreenPrompt").style.display="none" ; localStorage.setItem("dismisshomescreen", new Date());'><b><?php echo $translation["cancel_btntxt"][$lang] ?></b></button>
    </div>
    <div class="dsecimg">
      <div>
        <svg viewBox="0 0 120 169">
          <g fill="currentColor">
            <path d="M60 0l28 28-2 2a586 586 0 0 0-4 4L64 15v90h-8V15L38 34l-4-4-2-2L60 0z"></path>
            <path d="M0 49h44v8H8v104h104V57H76v-8h44v120H0V49z"></path>
          </g>
        </svg>
        <p><?php echo $translation["addhomeios-text1"][$lang] ?></p>
      </div>
      <div class="dsecimg2">
        <svg viewBox="55.99425507 31.98999977 157.76574707 157.76371765">
          <path fill="#62529c" d="M90.49 32.83a54.6 54.6 0 019.55-.84c23.98.03 47.96 0 71.94.01 8.5.07 17.3 1.74 24.4 6.65 10.94 7.28 16.52 20.54 17.35 33.3.06 26.03 0 52.06.03 78.08 0 10.16-3.59 20.56-10.95 27.73-7.93 7.61-18.94 11.43-29.79 11.98-25.71.03-51.42 0-77.12.01-10.37-.11-21.01-3.77-28.17-11.48-8.22-8.9-11.72-21.29-11.73-33.21.01-23.03-.03-46.05.02-69.07-.01-9.14 1.33-18.71 6.65-26.4 6.21-9.4 16.97-14.79 27.82-16.76m38.18 41.09c-.05 10.25.01 20.5 0 30.75-9.58-.03-19.16.02-28.75-.04-2.27.08-4.98-.25-6.68 1.61-2.84 2.34-2.75 7.12.01 9.48 1.8 1.69 4.46 1.57 6.75 1.64 9.56-.04 19.12-.01 28.67-.03.02 10.24-.06 20.48.01 30.72-.14 2.66 1.36 5.4 3.95 6.3 3.66 1.66 8.52-1.13 8.61-5.23.26-10.59.02-21.2.09-31.79 9.88 0 19.76.02 29.64.01 2.74.12 5.85-.67 7.14-3.34 2.23-3.75-.61-9.34-5.08-9.29-10.57-.14-21.14-.01-31.7-.04-.01-10.25.04-20.49 0-30.74.3-3.5-2.66-7.09-6.3-6.79-3.65-.33-6.66 3.26-6.36 6.78z"></path>
          <path fill="transparent" d="M128.67 73.92c-.3-3.52 2.71-7.11 6.36-6.78 3.64-.3 6.6 3.29 6.3 6.79.04 10.25-.01 20.49 0 30.74 10.56.03 21.13-.1 31.7.04 4.47-.05 7.31 5.54 5.08 9.29-1.29 2.67-4.4 3.46-7.14 3.34-9.88.01-19.76-.01-29.64-.01-.07 10.59.17 21.2-.09 31.79-.09 4.1-4.95 6.89-8.61 5.23-2.59-.9-4.09-3.64-3.95-6.3-.07-10.24.01-20.48-.01-30.72-9.55.02-19.11-.01-28.67.03-2.29-.07-4.95.05-6.75-1.64-2.76-2.36-2.85-7.14-.01-9.48 1.7-1.86 4.41-1.53 6.68-1.61 9.59.06 19.17.01 28.75.04.01-10.25-.05-20.5 0-30.75z"></path>
        </svg>
        <p><?php echo $translation["addhomeios-text2"][$lang] ?></p>
      </div>
    </div>
  </div>


  <!--Script!-->
  <script>
    //Auto Suggestion
    const choices = ["<?php echo implode('","', $transbuilding); ?>"];
    if (document.getElementById("Startbd") && document.getElementById("Destbd")) {
      autocomplete(document.getElementById("Startbd"), choices);
      autocomplete(document.getElementById("Destbd"), choices);
    }
  </script>
  
  <script type="text/javascript" src="Essential/component.js?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>"></script>

  </body>

</html> 
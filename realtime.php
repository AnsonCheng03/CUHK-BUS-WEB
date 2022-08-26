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
    <link rel="apple-touch-icon" href="Images/bus.ico" />
    <link rel="stylesheet" href="Essential/realtime.css?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>">
    <script src="Essential/realtime.js?v=<?php echo bin2hex(openssl_random_pseudo_bytes(32)) ?>"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3579541618707661" crossorigin="anonymous"></script>
</head>

<body>

<?php
    include('Essential/functions/functions.php');
    $lang = urlquery("lang") == "en" ? 1 : 0;
    include('Essential/functions/initdatas.php');
?>


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

    <?php






    foreach ($bus as $busnum) {
        foreach ($busnum["stations"]["name"] as $busstops) {
            if (isset($busstops)) {
                $allbusstop[] = $busstops;
            }
        }
    }
    $allbusstop =  array_filter(array_unique($allbusstop));


    ?>

    <form class="stopselector" onchange="submitform(this, '.realtimeresult', 'realtime/index.php');">
        <span><?php echo $translation['DescTxt-yrloc'][$lang]?>
            <img class="image-wrapper" src="Images/GPS.jpg" id="Dest-GPS-box" onclick="getLocation(this.id);"></img>
        </span>
        <select mode="station" class="select-box" name="Dest" id="Dest">
            <option disabled selected><?php echo $translation['DescTxt2'][$lang]?></option>
            <?php
            foreach ($allbusstop as $value)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
            ?>
        </select>
        <input type="hidden" name="lang" hidden value="<?php echo $lang ?>"></input>
    </form>


    <div class="realtimeresult"></div>



</body>

</html>
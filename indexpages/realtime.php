<?php
include_once('Essential/functions/functions.php');
$lang = urlquery("lang") == "en" ? 1 : 0;
include('Essential/functions/initdatas.php');
?>

<html>

<head>
    <title><?php echo $translation['title_realtime'][$lang]; ?> | 中大校巴資訊站 CU BUS INFOPAGE</title>
    <meta charset="utf-8">
    <meta name="title" content="<?php echo $translation['title_realtime'][$lang]; ?> | 中大校巴資訊站 CU BUS INFOPAGE">
    <meta http-equiv="Content-Language" content="<?php echo $lang == 1 ? "en" : "zh" ?>">
    <meta name="description" content="<?php echo $translation['meta_desc_realtime'][$lang]; ?> ">
    <meta name="keywords" content="CUHK, 中大, 香港中文大學, The Chinese University of Hong Kong, BUS, CUBUS, 巴士, 校巴, School Bus, 路線, route, 校巴站, busstop">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="revisit-after" content="7 days">
    <meta name="author" content="Anson Cheng">
    <link rel="icon" href="Images/bus.ico" type="image/x-icon">
    <link rel="shortcut icon" href="Images/bus.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="manifest/manifest.webmanifest">
    <meta name="application-name" content="CU BUS">
    <meta name="msapplication-TileColor" content="#62529c">
    <meta name="msapplication-TileImage" content="Images/bus.jpg">
    <meta name="msapplication-navbutton-color" content="#62529c">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="Images/bus.ico" />
    <meta name="MobileOptimized" content="320" />
    <meta name="google" content="notranslate">
    <meta name="google" value="notranslate">
    <link rel="stylesheet" href="Essential/realtime.css?v=<?php echo $version ?>">
    <script src="Essential/realtime.js?v=<?php echo $version ?>"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KCD7N2ZG3H"></script>

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-KCD7N2ZG3H');
    </script>


    <?php
    if ($placeads === true)
        echo "
            <script>
                const s = document.createElement('script');
                s.src = 'https://waitheja.net/400/5344478';
                try {
                    (document.body || document.documentElement).appendChild(s);
                } catch (e) {}
            </script>
        ";
    ?>
</head>

<body>

    <div class="navbar">
        <div class="back nav">
            <button onclick="append_query('mode','route');" /><?php echo $translation["back_btn"][$lang] ?></button>
        </div>
        <div class="lang-selector nav">
            <button onclick="append_query('lang','tc');" />中文</button>
            <button onclick="append_query('lang','en');" />ENG</button>
        </div>
    </div>

    <h1><?php echo $translation['title_realtime'][$lang]; ?></h1>

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

    <form class="stopselector" method="POST" onchange="submitform(this, '.realtimeresult', 'realtime/index.php');">
        <span><?php echo $translation['DescTxt-yrloc'][$lang] ?>
            <img alt="Get Current Location" class="image-wrapper" src="Images/GPS.jpg" id="Dest-GPS-box" onclick="getLocation(this.id);"></img>
        </span>
        <select mode="station" class="select-box" name="Dest" id="Dest">
            <option disabled selected><?php echo $translation['DescTxt2'][$lang] ?></option>
            <?php
            foreach ($allbusstop as $value)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
            ?>
        </select>
        <input type="hidden" name="lang" hidden value="<?php echo $lang ?>"></input>
        <input type="hidden" name="loop" hidden value="0"></input>
    </form>


    <div class="realtimeresult"></div>

    <!-- Website Suggestions-->
    <?
    echo "<div class='websitesugg'><div class='headingt'>" . $translation['website_suggest'][$lang] . "</div>";
    foreach ($WebsiteLinks as $row) {
        if ($row[0][$lang])
            echo "<a target='_blank' class='websites'  href='" . $row[1] . "'>" . $row[0][$lang] . "</a>";
    }
    echo "</div>";
    ?>

    <script type="text/javascript" src="https://s.skimresources.com/js/221050X1702490.skimlinks.js"></script>

</body>

<footer>

    <script>
        function onlineofflineswitch(innertext, color) {
            const element = document.createElement('div');
            element.classList.add('networkerror');
            element.style.backgroundColor = color;
            element.innerHTML = "<?php echo "<p class='heading'>" . $translation["internet_unstable"][$lang] . "</p><p> \" + innertext + \" </p>"; ?>"
            document.body.appendChild(element);
            setTimeout(() => {
                element.style.opacity = 0;
            }, 2000);
            setTimeout(() => {
                element.remove();
            }, 3000);
        }

        window.addEventListener('online', () => {
            onlineofflineswitch("<?php echo $translation["internet_online"][$lang]; ?>", "#23C552")
        });
        window.addEventListener('offline', () => {
            onlineofflineswitch("<?php echo $translation["internet_offline"][$lang]; ?>", "#F84F31")
        });
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service-worker.js').then(function(reg) {}).catch(function(err) {})
        }
    </script>
</footer>

</html>
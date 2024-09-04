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
    $allbusstop = array_filter(array_unique($allbusstop));
    sort($allbusstop);

    ?>

    <form class="stopselector" method="POST" onchange="submitform(this, '.realtimeresult', 'realtime/index.php');">
        <span><?php echo $translation['DescTxt-yrloc'][$lang] ?>
            <img alt="Get Current Location" class="image-wrapper" src="Images/GPS.jpg" id="Dest-GPS-box"
                onclick="getLocation(this.id);"></img>
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
    <?php
    echo "<div class='websitesugg'><div class='headingt'>" . $translation['website_suggest'][$lang] . "</div>";
    foreach ($WebsiteLinks as $row) {
        if ($row[0][$lang])
            echo "<a target='_blank' class='websites'  href='" . $row[1] . "'>" . $row[0][$lang] . "</a>";
    }
    echo "</div>";
    ?>
</body>
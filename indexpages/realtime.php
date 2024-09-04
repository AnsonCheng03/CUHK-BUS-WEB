<body>

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
        <span><?php echo $translation['DescTxt-yrloc'][$lang] ?></span>
        <select mode="station" class="select-box" name="Dest" id="Dest">
            <option disabled selected><?php echo $translation['DescTxt2'][$lang] ?></option>
            <?php
            foreach ($allbusstop as $value)
                echo '<option value="' . $value . '">' . $translation[$value][$lang] . "</option>";
            ?>
        </select>
        <img alt="Get Current Location" class="image-wrapper" src="Images/GPS.jpg" id="Dest-GPS-box"
            onclick="getLocation(this.id);"></img>
        <input type="hidden" name="lang" hidden value="<?php echo $lang ?>"></input>
        <input type="hidden" name="loop" hidden value="0"></input>
    </form>


    <div class="realtimeresult"></div>

</body>
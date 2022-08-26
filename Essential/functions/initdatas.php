<?php

foreach (csv_to_array("Data/Route") as $busno) {
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
foreach (array_slice(csv_to_array("Data/Translate"), 1) as $row) {
    if ($row[0] !== "" && substr($row[0], 0, 2) !== "//") {
        $translation[$row[0]] = array($row[2], $row[3]);
        $translation[$row[0]][] = $row[1];
    }
}

//Buildings
foreach (array_slice(csv_to_array("Data/Station"), 1) as $row) {
    $station[$row[1]][] = $row[0];
}

//Notice
foreach (array_slice(csv_to_array("Data/Notice"), 1) as $index => $row) {
    $notice[$index]["content"] = array_slice($row, 1);
    $notice[$index]["pref"]["type"] = $row[0];
}

//GPS
foreach (array_slice(csv_to_array("Data/GPS"), 1) as $row) {
    $GPS[$row[0]]["Lat"] = $row[1];
    $GPS[$row[0]]["Lng"] = $row[2];
}

?>

<script>
    //Load building name to js
    const Translation = [];
    <?php
    foreach ($translation as $name => $value)
        if (end($value))
            echo 'Translation["' . $name . '"] = "' . $value[$lang] . '";';
    ?>

    const GPSdata = [
        <?php
        foreach ($GPS as $name => $location) {
            $nameattr = substr(strstr($name, '|', false), 1) ?: "";
            $name = strstr($name, '|', true) ?: $name;
            try {
                if ($location["Lat"] . $location["Lng"])
                    echo "{ \"location\": \"" . $translation[$name][$lang] . "\",  \"lat\": \"" . $location["Lat"] . "\",  \"lng\": \"" . $location["Lng"] . "\", \"attr\" : \"" . $translation[$nameattr][$lang] . "\" , \"code\" : \"" . $name . "\"  }, ";
            } catch (Exception $e) { }
        }
        ?>
    ];
</script>
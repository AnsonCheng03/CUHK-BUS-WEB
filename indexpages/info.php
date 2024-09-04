<?php
echo "<div class='websitesugg'><div class='headingt'>" . $translation['website_suggest'][$lang] . "</div>";
foreach ($WebsiteLinks as $row) {
    if ($row[0][$lang])
        echo "<a target='_blank' class='websites'  href='" . $row[1] . "'>" . $row[0][$lang] . "</a>";
}
echo "</div>";
?>
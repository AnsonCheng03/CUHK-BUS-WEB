<?php
echo "<div class='websitesugg'><div class='headingt'>" . $translation['website_suggest'][$lang] . "</div>";
foreach ($WebsiteLinks as $row) {
    if ($row[0][$lang])
        echo "<a target='_blank' class='websites'  href='" . $row[1] . "'>" . $row[0][$lang] . "</a>";
}
echo "</div>";
?>


<div class="abouts">
    <!-- About !-->

    <?php
    if ($lang == 0) {
        echo "<div class='websitesugg aboutdiv'><div class='headingt'>" . $translation['about_page'][$lang] . "</div>
      <p><a target='_blank' class='abouttxt'>
        中大校巴資訊站是由中大本校學生建立。由於中文大學校方並無提供過多校巴資訊，學生難以得知校巴何時到站。
        同時，校巴路線繁多，新入學同學難以迅速找到自己需要乘搭什麼校巴。
        為此，本人建立了中大校巴資訊站，本站提供點對點路線搜尋、實時校巴查詢服務，亦讓學生實時報告校巴位置，讓中大學生輕鬆在校園穿梭。
        <br><br>本站具有以下功能：<br>
        - 可選擇中大建築物作起點/終點<br>
        - 尋找校巴路線時可同時搜尋上下行車站<br>
        - 提供校巴轉車方案<br>
        - 自動篩選服務時間外之校巴<br>
        - 可預先查看某日之路線<br>
        - 不用下載即可使用<br>
        - 離線時亦可查看地圖<br>
        - 可加入主畫面使用<br>
      </a></p>";
        echo "</div>";
    }
    ?>
</div>
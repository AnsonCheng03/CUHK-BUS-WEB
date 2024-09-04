<?php
$version = "1.2.7";
$placeads = false;

include_once('Essential/functions/functions.php');
$lang = urlquery("lang") == "en" ? 1 : 0;
date_default_timezone_set("Asia/Hong_Kong");
include('Essential/functions/initdatas.php');

// detect current page
if (urlquery('mode') == 'realtime')
  $currentpage = 'realtime';
else
  $currentpage = 'routeselection';
?>


<html>

<head>
  <?php
  if ($currentpage == 'realtime') {
    echo "<title>" . $translation['title_realtime'][$lang] . " | 中大校巴資訊站 CU BUS INFOPAGE</title>";
    echo "<meta name='title' content='" . $translation['title_realtime'][$lang] . " | 中大校巴資訊站 CU BUS INFOPAGE'>";
    echo "<meta name='description' content='" . $translation['meta_desc_realtime'][$lang] . "'>";
    echo "<link rel='stylesheet' href='Essential/realtime.css?v=" . $version . "'>";
    echo "<script src='Essential/realtime.js?v=" . $version . "'></script>";
  } else {
    echo "<title>" . $translation['title_routesearch'][$lang] . " | 中大校巴資訊站 CU BUS INFOPAGE</title>";
    echo "<meta name='title' content='" . $translation['title_routesearch'][$lang] . " | 中大校巴資訊站 CU BUS INFOPAGE'>";
    echo "<meta name='description' content='" . $translation['meta_desc_routesearch'][$lang] . "'>";
    echo "<link rel='stylesheet' href='Essential/mainpage.css?v=" . $version . "'>";
    echo "<script src='Essential/mainpage.js?v=" . $version . "'></script>";
  }
  ?>
  <meta charset="utf-8">
  <meta http-equiv="Content-Language" content="<?php echo $lang == 1 ? "en" : "zh" ?>">
  <meta name="keywords"
    content="CUHK, 中大, 香港中文大學, The Chinese University of Hong Kong, BUS, CUBUS, 巴士, 校巴, School Bus, 路線, route, 校巴站, busstop">
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

</head>

<?php
if ($currentpage == 'realtime')
  include('indexpages/realtime.php');
else
  include('indexpages/routeselection.php');
?>

<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3579541618707661" data-ad-slot="8668958470"
  data-ad-format="auto" data-full-width-responsive="true"></ins>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({});
</script>




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
    navigator.serviceWorker.register('service-worker.js').then(function (reg) { }).catch(function (err) { })
  }
</script>

</html>
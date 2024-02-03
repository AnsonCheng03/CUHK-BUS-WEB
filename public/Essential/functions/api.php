<?php
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set("Asia/Hong_Kong");

include(__DIR__ . '/functions.php');

function haversineGreatCircleDistance(
  $latitudeFrom,
  $longitudeFrom,
  $latitudeTo,
  $longitudeTo,
  $earthRadius = 6371000
) {
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

// if (!isset($_POST['CSRF']) || $_POST['CSRF'] != $_SESSION['_token']) {
//   http_response_code(403);
//   die("Token Error!");
// }

// $conn = new mysqli("localhost", "u392756974_cubus", "*rV0J2J5", "u392756974_cubus");
$conn = new mysqli("217.21.74.51", "u392756974_cubus", "*rV0J2J5", "u392756974_cubus");

if ($conn->connect_error) {
  http_response_code(500);
  die("Connection failed: " . $conn->connect_error);
}

$lang = $_POST["lang"] == "en" ? "ENG" : "中文";



function getRoute($startingStation, $endingStation, $Route, $selectedTime, $stationTranslate)
{
  // $Route = [["2","3","5","8","1A","1B","N","H","6A","6B"],["N"]]

  $SearchRoute = [];
  $conn = new mysqli("217.21.74.51", "u392756974_cubus", "*rV0J2J5", "u392756974_cubus");

  // duplicate Route to make [["2","3","5","8","1A","1B","N","H","6A","6B"],["N"]] to [["2","3","5",...,"2#","3#","5#",...],["N,N#"]]
  // remember to keep the original Route
  $SpecialRoute = array_map(function ($item) {
    return array_map(fn($i) => $i . "#", $item);
  }, $Route);

  foreach ($Route as $index => $routeItem) {
    $Route[$index] = array_merge($routeItem, $SpecialRoute[$index]);
  }

  // foreach merge Route
  foreach (array_merge(...$Route) as $busNo) {
    // get all route
    $stmt = $conn->prepare("SELECT `Route` FROM `Route` WHERE `BUSNO` = ?;");
    $stmt->bind_param("s", $busNo);
    $stmt->execute();
    $routeResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0]["Route"];
    if (!$routeResult)
      continue;
    $SearchRoute[$busNo]["Route"] = $routeResult;
    $SearchRoute[$busNo]["Route"] = json_decode($SearchRoute[$busNo]["Route"], true);

    // check index of startingStation and endingStation
    $tmpSearch = array_filter($SearchRoute[$busNo]["Route"], function ($station) use ($startingStation, $endingStation) {
      return $station[0] === $startingStation || $station[0] === $endingStation;
    });
    $startingStationIndex = array_search($startingStation, array_column($tmpSearch, 0));
    $endingStationIndex = -1;
    do {
      $tmpEndingStationIndex = array_search($endingStation, array_column($tmpSearch, 0));
      if ($tmpEndingStationIndex !== false) {
        $endingStationIndex = $tmpEndingStationIndex;
        break;
      }
    } while ($tmpEndingStationIndex !== false && $endingStationIndex < $startingStationIndex);

    if ($startingStationIndex === false || $endingStationIndex === false || $endingStationIndex < $startingStationIndex) {
      unset($SearchRoute[$busNo]);
      continue;
    } else {
      // get array key from startingStationIndex and endingStationIndex
      $startingStationKey = array_keys($tmpSearch)[$startingStationIndex] - 1;
      $endingStationKey = array_keys($tmpSearch)[$endingStationIndex];
      $SearchRoute[$busNo]["Route"] = array_slice($SearchRoute[$busNo]["Route"], $startingStationKey, $endingStationKey - $startingStationKey);
    }

    // add bus name
    $SearchRoute[$busNo]["Details"]["BusNo"] = $busNo;

    // calculate time
    $SearchRoute[$busNo]["Details"]["Time"] = array_reduce($SearchRoute[$busNo]["Route"], function ($carry, $item) {
      return $carry + $item[2];
    }, 0) - $SearchRoute[$busNo]["Route"][0][2];

    // check arrival time
    $timetable = json_decode(file_get_contents(__DIR__ . "/../../Data/timetable.json"), true);
    $busTime = $timetable[$SearchRoute[$busNo]["Route"][0][0] . "|" . $SearchRoute[$busNo]["Route"][0][1]][$busNo];
    $busTime = array_map(function ($item) {
      return strtotime($item);
    }, $busTime);
    $busTime = array_filter($busTime, function ($item) use ($selectedTime) {
      return $item > $selectedTime;
    });
    $busTime = array_slice($busTime, 0, 5);
    $SearchRoute[$busNo]["Details"]["ArrivalTime"] = $busTime;

    // if bus is going expire(In Route[1] but not Route[0]), add warning
    if (in_array($busNo, $Route[1]) && !in_array($busNo, $Route[0])) {
      $SearchRoute[$busNo]["Details"]["Warning"][] = "EXPIRE";
    }

    // replace $SearchRoute[$busNo]["Route"] with chinese
    foreach ($SearchRoute[$busNo]["Route"] as $index => $station) {
      foreach ($stationTranslate as $stationItem) {
        if ($stationItem["Code"] == $station[0]) {
          $SearchRoute[$busNo]["Route"][$index][0] = $stationItem["中文"];
          break;
        }
      }
    }

  }
  $conn->close();


  return $SearchRoute;
}

switch ($_POST['action']) {
  case "getData":
    $stmt = $conn->prepare("SELECT * FROM `translatebuilding`;");
    $stmt->execute();
    $buildingResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt = $conn->prepare("SELECT * FROM `translateroute`;");
    $stmt->execute();
    $stationResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $conn->close();

    $result = array_map(function ($item) use ($lang) {
      return [$item["Code"], $item[$lang], "building"];
    }, $buildingResult);
    $result = array_merge($result, array_map(function ($item) use ($lang) {
      return [$item["Code"], $item[$lang], "station"];
    }, $stationResult));

    die(json_encode($result));

  case "getGPSNearest":
    $clientLat = $_POST["lat"];
    $clientLng = $_POST["lng"];

    $stmt = $conn->prepare("SELECT * FROM `gps`;");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM `translateroute`;");
    $stmt->execute();
    $stationTranslate = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    $result = array_map(function ($item) use ($clientLat, $clientLng) {

      if (!$item["Lat"] || !$item["Lng"] || !$clientLat || !$clientLng)
        $item["distance"] = 999999999;
      else
        $item["distance"]
          = haversineGreatCircleDistance($item["Lat"], $item["Lng"], $clientLat, $clientLng);

      return $item;
    }, $result);

    usort($result, function ($a, $b) {
      return $a["distance"] - $b["distance"];
    });

    $result = array_slice($result, 0, 5);

    // get Location, check if it is in the stationTranslate , if yes, put it in $item["Name"]
    $result = array_map(function ($item) use ($stationTranslate) {
      $item["Name"] = $item["Location"];
      foreach ($stationTranslate as $station) {
        if ($station["Code"] == $item["Location"]) {
          $item["Name"] = $station["中文"];
          break;
        }
      }
      return $item;
    }, $result);


    die(json_encode($result));


  case "getRoute":

    $stmt = $conn->prepare("SELECT * FROM `translatebuilding`;");
    $stmt->execute();
    $buildingResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt = $conn->prepare("SELECT * FROM `translateroute`;");
    $stmt->execute();
    $stationResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $Route = $_POST["departNow"] ? json_decode($_POST["departNow"], true) : json_decode($_POST["Route"], true);
    $outputRoute = getRoute($_POST["startLocation"], $_POST["endLocation"], $Route, time(), array_merge($buildingResult, $stationResult));

    usort($outputRoute, function ($a, $b) {
      return $a[1]["ArrivalTime"][0] - $b[1]["ArrivalTime"][0];
    });

    die(json_encode($outputRoute));


  default:
    $conn->close();
    http_response_code(404);
    die("Action not found");
}


<?php

function download_files($filename, $url, $skipfilesize = false, $forcedownload = false)
{
    if (file_exists($filename)) $moditime = filemtime($filename);
    else $moditime = 0;
    if (file_exists($filename)) $filesize = filesize($filename);
    else $filesize = 0;

    if ($skipfilesize == true) {
        $filesize = 10000;
    }

    if (($moditime + 15 * 60) <= time() || $filesize < 300 || $forcedownload) {
        $source = file_get_contents($url);
        file_put_contents($filename, $source);
    }
}

function pingAddress($ip)
{
    if ($socket = @fsockopen($ip, 443, $errno, $errstr, 30)) {
        return true;
        fclose($socket);
    } else {
        return false;
    }
}

if (isset($_SERVER['REMOTE_ADDR'])) {
    header('HTTP/1.0 403 Forbidden');
    die('No Permission');
}

date_default_timezone_set('Asia/Hong_Kong');
$host = 'www.transport.cuhk.edu.hk';
include('functions.php');

if (pingAddress(gethostbyname($host))) {
    $html = file_get_contents('https://' . $host . '/', 0, stream_context_create(["http" => ["timeout" => 20]]));
    if ($html === false) {
        $savestatus["ERROR"] = "fetch";
    } else {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        foreach ((new DOMXPath($dom))->query('//span[contains(@class, "hr-status")]') as $html) {
            try {
                //Bus name
                $busno = trim(str_replace('ROUTE', '', strtoupper($html->parentNode->nodeValue)));

                //Bus Status
                switch ($html->getAttribute('class')) {
                    case "hr-status hr-status-delayed":
                        $status = "delayed";
                        break;
                    case "hr-status hr-status-suspended":
                        $status = "suspended";
                        break;
                    case "hr-status hr-status-normal":
                        $status = "normal";
                        break;
                    default:
                        $status = "no";
                }

                //Save to array
                $savestatus[$busno] = $status;
                $bus[$busno]["stats"]["status"] = $status;
                if (isset($bus[$busno . "#"])) {
                    $bus[$busno . "#"]["stats"]["status"] = $status;
                }
            } catch (Exception $e) {
            }
        }
    }
} else {
    $savestatus["ERROR"] = "ping";
}



$tempArray = json_decode(file_get_contents(__DIR__ . '/../../Data/Status.json'), true);
$tempArray[(new DateTime())->format('Y-m-d H:i:s')] = $savestatus;
file_put_contents(__DIR__ . '/../../Data/Status.json', json_encode(array_slice($tempArray, -1500), JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/../../Data/Status-' . (new DateTime())->format('Y-m-d') . '.json', json_encode(array_slice($tempArray, -1500), JSON_PRETTY_PRINT));

download_files(__DIR__ . "/../../Data/Route.csv", 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1092784054&single=true&output=csv');
download_files(__DIR__ . "/../../Data/Translate.csv", 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=13523980&single=true&output=csv');
download_files(__DIR__ . "/../../Data/Station.csv", "https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1424772552&single=true&output=csv");
download_files(__DIR__ . "/../../Data/Notice.csv", "https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1434460943&single=true&output=csv", true);
download_files(__DIR__ . "/../../Data/GPS.csv", "https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=1636019387&single=true&output=csv", true);
download_files(__DIR__ . "/../../Data/Websites.csv", "https://docs.google.com/spreadsheets/d/e/2PACX-1vRn-9WBygVxJTJQkmie8LDxC-R2Oedma14PWVT0LW6v85smPOd9AhqhkZ9LfYzire2iaGb0pIGVpdmr/pub?gid=336913037&single=true&output=csv", true);

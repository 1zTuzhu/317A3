<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\ModelManager;

$xml = simplexml_load_file("select_date.xml");
$record = $xml->record[0];

$station = intval($record->site);
$year = intval($record->year);
$month = intval($record->month);
$day = intval($record->day);
$sample = [$month, $day];

$modelManager = new ModelManager();
$model_max_temp = $modelManager->restoreFromFile(__DIR__ . "/{$station}_maxTempModel.dat");
$model_min_temp = $modelManager->restoreFromFile(__DIR__ . "/{$station}_minTempModel.dat");
$model_max_humidity = $modelManager->restoreFromFile(__DIR__ . "/{$station}_maxHumidityModel.dat");
$model_min_humidity = $modelManager->restoreFromFile(__DIR__ . "/{$station}_minHumidityModel.dat");

$max_temp = round($model_max_temp->predict($sample), 1);
$min_temp = round($model_min_temp->predict($sample), 1);
$max_humidity = round($model_max_humidity->predict($sample), 1);
$min_humidity = round($model_min_humidity->predict($sample), 1);

$siteNames = [
	91107 => "Wynyard",
	91237 => "Launceston",
	91292 => "Smithton",
	94029 => "Hobart",
	94212 => "Campania"
];
$site_name = $siteNames[$station] ?? "Unknown Station";

$entry = new stdClass();
$entry->station = $station;
$entry->site_name = $site_name;
$entry->date = sprintf("2022-%02d-%02d", $month, $day);
$entry->max_temp = $max_temp;
$entry->min_temp = $min_temp;
$entry->max_humidity = $max_humidity;
$entry->min_humidity = $min_humidity;

echo json_encode($entry, JSON_PRETTY_PRINT);

?>

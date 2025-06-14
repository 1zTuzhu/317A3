<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-type: text/plain');
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Regression\LeastSquares;
use Phpml\ModelManager;

$t_ = time();
$data = json_decode(file_get_contents("all_station.json"));
$modelManager = new ModelManager();
$sites = [];

foreach ($data as $row) {
    $site = intval($row->station);
    $date = strtotime($row->date);    
    $month = (int)date("n",$date);
    $day = (int)date("j", $date);    
    $sample = [$month, $day];
    //adding data to their respective sites
    $sites[$site]['samples'][] = $sample;
    $sites[$site]['max_temp'][] = floatval($row->max_Temperature);
    $sites[$site]['min_temp'][] = floatval($row->min_Temperature);
    $sites[$site]['max_humidity'][] = floatval($row->max_Humidity);
    $sites[$site]['min_humidity'][] = floatval($row->min_Humidity);
}

foreach($sites as $site => $data) {

    $samples = $data['samples'];

    $max_temp = new LeastSquares();
    $min_temp = new LeastSquares();
    $max_humidity = new LeastSquares();
    $min_humidity = new LeastSquares();

    $max_temp->train($samples, $data['max_temp']);
    $min_temp->train($samples, $data['min_temp']);
    $max_humidity->train($samples, $data['max_humidity']);
    $min_humidity->train($samples, $data['min_humidity']);

    $modelManager->saveToFile($max_temp, __DIR__ . "/{$site}_maxTempModel.dat");
    $modelManager->saveToFile($min_temp, __DIR__ . "/{$site}_minTempModel.dat");
    $modelManager->saveToFile($max_humidity, __DIR__ . "/{$site}_maxHumidityModel.dat");
    $modelManager->saveToFile($min_humidity, __DIR__ . "/{$site}_minHumidityModel.dat");
}
echo "Training completed in " . (time() - $t_) . " seconds.\n";

?>

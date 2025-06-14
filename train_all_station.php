<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-type: text/plain');
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Regression\LeastSquares;
use Phpml\ModelManager;

$samples = [];
$temp_max = [];
$temp_min = [];
$hum_max = [];
$hum_min = [];

$t_ = time();

$data = json_decode(file_get_contents("all_station.json"));

foreach ($data as $row) {
    $site = intval($row->station);
    $date = strtotime($row->date);
    
    $year = (int)date("Y", $date);
    $month = (int)date("n",$date);
    $day = (int)date("j", $date);
    
    $samples[] = [$site, $year, $month, $day];
    
    $max_temp[] =floatval($row -> max_Temperature);
    $min_temp[] =floatval($row -> min_Temperature);
    $max_humidity[] = floatval($row -> max_Humidity);
    $min_humidity[] = floatval($row -> min_Humidity);
}

$model_max_temp = new LeastSquares(); $model_max_temp->train($samples, $max_temp);
$model_min_temp = new LeastSquares(); $model_min_temp->train($samples, $min_temp);
$model_max_humidity = new LeastSquares(); $model_max_humidity->train($samples, $max_humidity);
$model_min_humidity = new LeastSquares(); $model_min_humidity->train($samples, $min_humidity);


$modelManager = new ModelManager();
$modelManager->saveToFile($model_max_temp, 'model_max_temp.dat');
$modelManager->saveToFile($model_min_temp, 'model_min_temp.dat');
$modelManager->saveToFile($model_max_humidity, 'model_max_humidity.dat');
$modelManager->saveToFile($model_min_humidity, 'model_min_humidity.dat');

echo "Trainning finished in " . (time() - $t_) . "s\n";
$test_sample = [94029, 2022, 6, 15];
$predicted_max_temp = $model_max_temp->predict($test_sample);
$predicted_min_temp = $model_min_temp->predict($test_sample);
$predicted_max_humidity = $model_max_humidity->predict($test_sample);
$predicted_min_humidity = $model_min_humidity->predict($test_sample);

echo "pridiction result: station 94029 on 2022-06-15's max temp is $predicted_max_temp °C\n";
echo "pridiction result: station 94029 on 2022-06-15's min temp is $predicted_min_temp °C\n";
echo "pridiction result: station 94029 on 2022-06-15's max humidity is $predicted_max_humidity %\n";
echo "pridiction result: station 94029 on 2022-06-15's min humidity is $predicted_min_humidity %\n";

?>

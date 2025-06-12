<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (
    isset($_GET['day']) &&
    isset($_GET['month']) &&
    isset($_GET['year']) &&
    isset($_GET['site'])
) {
    $day = $_GET['day'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $site = $_GET['site'];

    $filename = "select_date.xml";

    $newData = "\n<record>\n"
             . "\t<day>$day</day>\n"
             . "\t<month>$month</month>\n"
             . "\t<year>$year</year>\n"
             . "\t<site>$site</site>\n"
             . "</record>\n";
             
    $str = "<?xml version='1.0' encoding='UTF-8'?>\n<records>$newData</records>";
    file_put_contents($filename, $str);
    
    echo "Write Successful";
} else {
    echo "0";
}
?>

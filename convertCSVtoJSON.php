<?php

$inputCsv = __DIR__ . "/91107_clean.csv";
$outputJson = __DIR__ . "/91107_clean.json";

$data = [];
$headers = [];

if (($handle = fopen($inputCsv, "r")) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
        if (empty($headers)) {
            $headers = array_map(function($h) {
                return preg_replace('/^\xEF\xBB\xBF/', '', $h);
            }, $row);
        } else {
            $record = array_combine($headers, $row);
            $data[] = $record;
        }
    }
    fclose($handle);
}

file_put_contents(
    $outputJson,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "Converted: $outputJson\n";
?>

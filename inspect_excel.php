<?php
require 'vendor/autoload.php';
use Spatie\SimpleExcel\SimpleExcelReader;

$rows = SimpleExcelReader::create('docs/formato-importacion-full.xlsx')->getRows();
foreach ($rows as $index => $row) {
    echo "FILA " . ($index + 1) . ": " . json_encode($row) . PHP_EOL;
}

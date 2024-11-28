<?php

require '../vendor/autoload.php'; // Include Composer autoloader

use PhpOffice\PhpSpreadsheet\IOFactory;

function getDataFormXlsx($filePath)
{
  $dataResult = array();
  try {
    // Load the spreadsheet file
    $spreadsheet = IOFactory::load($filePath);

    // Get the first sheet
    $sheet = $spreadsheet->getActiveSheet();

    // Convert the sheet data to an array
    $data = $sheet->toArray();

    // Remove the header row
    $dataResult = array_slice($data, 1);

    // Display the result
    return ($dataResult); // The result is stored in $dataResult
  } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    return $e->getMessage();
  }
}

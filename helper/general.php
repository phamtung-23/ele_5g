<?php
function saveDataToJson($data, $directory, $fileName)
{
  // Ensure the directory exists
  if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
  }

  $filePath = $directory . '/' . $fileName . '.json';

  // Save data to JSON file
  if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
    return ['status' => 'success'];
  } else {
    return ['status' => 'fail'];
  }
}

function getDataFromJson($filePath)
{
  // Check if the file exists
  if (!file_exists($filePath)) {
    return ['status' => 'fail', 'message' => 'File not found'];
  }

  // Read the file content
  $jsonContent = file_get_contents($filePath);

  // Decode the JSON data
  $data = json_decode($jsonContent, true);

  // Check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['status' => 'fail', 'message' => 'Error decoding JSON'];
  }

  return ['status' => 'success', 'data' => $data];
}

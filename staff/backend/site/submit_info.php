<?php
include '../../../helper/general.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [];
  foreach ($_POST as $key => $value) {
    $data[$key] = htmlspecialchars($value);
  }

  if (isset($data['station_name'])) {
    $stationName = $data['station_name'];
    $directory = '../../../database/site/submit';
    $response = saveDataToJson($data, $directory, $stationName);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail']);
  }
} else {
  echo json_encode(['status' => 'fail']);
}

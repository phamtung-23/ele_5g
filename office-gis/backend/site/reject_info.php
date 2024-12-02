<?php
include '../../../helper/general.php'; // Adjust the path as necessary
include '../../../helper/payment.php'; // Adjust the path as necessary

header('Content-Type: application/json');

// Get data from POST request
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if the data is valid
  if (isset($data['station_name'])) {
    $stationName = $data['station_name'];
    $userRole = $data['role'];
    $directory = '../../../database/site/dataSubmit';

    // Get previous data by station name
    $filePathDataPrevious = $directory . '/' . $stationName . '.json';
    $res = getDataFromJson($filePathDataPrevious);
    if ($res['status'] === 'success') {
      $dataPrevious = $res['data'];
    } else {
      echo json_encode(['status' => 'fail', 'message' => 'Previous data not found']);
      exit;
    }

    foreach ($dataPrevious['approval'] as $index => $approval) {
      if ($approval['role'] === $userRole) {
        // Update the status to approved
        $dataPrevious['approval'][$index]['email'] = $data['email'];
        $dataPrevious['approval'][$index]['status'] = 'rejected';
        $dataPrevious['approval'][$index]['updateTime'] = date('Y-m-d H:i:s');
        $dataPrevious['approval'][$index]['comment'] = $data['message'];
      }
    }




    // Save the updated data
    $response = updateDataToJson($dataPrevious, $directory, $stationName);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail', 'message' => 'Station name not provided']);
  }
} else {
  echo json_encode(['status' => 'fail', 'message' => 'Invalid request method']);
}

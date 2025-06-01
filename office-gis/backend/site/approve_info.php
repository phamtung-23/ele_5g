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

    // Update the site data with the submitted changes
    if (isset($data['site_data']) && is_array($data['site_data'])) {
      // Merge the updated fields with the previous data
      foreach ($data['site_data'] as $key => $value) {
        // Don't update image fields - we keep the existing image links
        if (!strpos($key, '_img') && !strpos($key, '_link')) {
          $dataPrevious[$key] = $value;
        }
      }
    }

    // Update the approval status
    foreach ($dataPrevious['approval'] as $index => $approval) {
      if ($approval['role'] === $userRole) {
        // Update the status to approved
        $dataPrevious['approval'][$index]['email'] = $data['email'];
        $dataPrevious['approval'][$index]['status'] = 'approved';
        $dataPrevious['approval'][$index]['updateTime'] = date('Y-m-d H:i:s');
      }
    }

    // Update the modified time
    $dataPrevious['updated_at'] = date('Y-m-d H:i:s');
    
    // Save the updated data
    $response = updateDataToJson($dataPrevious, $directory, $stationName);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail', 'message' => 'Station name not provided']);
  }
} else {
  echo json_encode(['status' => 'fail', 'message' => 'Invalid request method']);
}

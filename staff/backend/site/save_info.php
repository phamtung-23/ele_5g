<?php
include '../../../helper/general.php'; // Adjust the path as necessary
include '../../../helper/payment.php'; // Adjust the path as necessary
require '../../../library/google_api/vendor/autoload.php'; // Đảm bảo đường dẫn đúng

function uploadFileToGoogleDrive($filePath, $fileName, $folderId)
{
  $client = new Google_Client();
  $client->setAuthConfig('gdcredentials.json'); // Đường dẫn tới file credential
  $client->addScope(Google_Service_Drive::DRIVE_FILE);

  $service = new Google_Service_Drive($client);

  $fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $fileName,
    'parents' => [$folderId]
  ]);

  $content = file_get_contents($filePath);

  try {
    $file = $service->files->create($fileMetadata, [
      'data' => $content,
      'mimeType' => mime_content_type($filePath),
      'uploadType' => 'multipart'
    ]);
    return "https://drive.google.com/file/d/" . $file->id . "/view";
  } catch (Exception $e) {
    return null;
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [];
  // get reference station name
  $referenceStationName = isset($_POST['reference_station']) ? $_POST['reference_station'] : null;

  // get data from post request
  foreach ($_POST as $key => $value) {
    $data[$key] = htmlspecialchars($value);
  }

  if (isset($data['station_name'])) {
    $stationName = $data['station_name'];
    $directory = '../../../database/site/dataSubmit';

    // get reference data by reference station name
    $filePathDataReference = $directory . '/' . $referenceStationName . '.json';
    $res = getDataFromJson($filePathDataReference);
    if ($res['status'] === 'success') {
      $dataReference = $res['data'];
    } else {
      $dataReference = [];
    }

    // get previous data by station name, if not exist then set reference data if it exits
    $filePathDataPrevious = $directory . '/' . $stationName . '.json';
    $res = getDataFromJson($filePathDataPrevious);
    if ($res['status'] === 'success') {
      $dataPrevious = $res['data'];
    } else {
      $dataPrevious = $dataReference;
    }
    
    // Handle file uploads and save to Google Drive
    if (!empty($_FILES)) {
      foreach ($_FILES as $fileKey => $fileArray) {
        $uploadDir = $stationName . '_files/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        foreach ($fileArray['name'] as $index => $fileName) {
          $fileTmpName = $fileArray['tmp_name'][$index];
          $filePath = $uploadDir . basename($fileName);
          $folderId = '1702MmGw3Y5C1WKveLqxqhNUzBTD6e19C';

          if (move_uploaded_file($fileTmpName, $filePath)) {
            $linkImg = uploadFileToGoogleDrive($filePath, $fileName, $folderId);
            if ($linkImg) {
              $data[$fileKey][] = $linkImg;
              unlink($filePath);
            }else{
              // remove file if upload fail
              unlink($filePath);
            }
          }
        }
        // check $data[$fileKey] is empty or not, if empty then set previous data
        if (empty($data[$fileKey])) {
          // check previous data is empty or not
          if (!empty($dataPrevious)) {
            if (isset($dataPrevious[$fileKey])) {
              $data[$fileKey] = $dataPrevious[$fileKey];
            } 
          }
        }
        rmdir($uploadDir);
      }
    }

    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['status'] = 'updated';


    $response = saveDataToJson($data, $directory, $stationName);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail']);
  }
} else {
  echo json_encode(['status' => 'fail']);
}

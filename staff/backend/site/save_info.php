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
  foreach ($_POST as $key => $value) {
    $data[$key] = htmlspecialchars($value);
  }

  if (isset($data['station_name'])) {
    $stationName = $data['station_name'];
    $directory = '../../../database/site/save';

    // read data from json file
    $filePathDataPrevious = $directory . '/' . $stationName . '.json';
    $res = getDataFromJson($filePathDataPrevious);
    if ($res['status'] === 'success') {
      $dataPrevious = $res['data'];
    } else {
      $dataPrevious = [];
    }
    
    // Handle file uploads
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
          $data[$fileKey] = $dataPrevious[$fileKey];
        }
        rmdir($uploadDir);
      }
    }


    $response = saveDataToJson($data, $directory, $stationName);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail']);
  }
} else {
  echo json_encode(['status' => 'fail']);
}

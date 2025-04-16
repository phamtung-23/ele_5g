<?php
set_time_limit(1000);
include '../../../helper/general.php'; // Adjust the path as necessary
include '../../../helper/payment.php'; // Adjust the path as necessary
require '../../../library/google_api/vendor/autoload.php'; // Ensure the path is correct

function initializeGoogleDriveClient()
{
  $client = new Google_Client();
  $client->setAuthConfig('gdcredentials.json'); // Path to credentials file
  $client->addScope(Google_Service_Drive::DRIVE_FILE);
  $service = new Google_Service_Drive($client);

  return $service;
}

function uploadFileToGoogleDrive($service, $filePath, $fileName, $folderId)
{
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
    logEntry($e->getMessage());
    return null;
  }
}

// Only execute the following logic if this file is accessed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $referenceStationName = isset($_POST['reference_station']) ? $_POST['reference_station'] : null;

    foreach ($_POST as $key => $value) {
      $data[$key] = htmlspecialchars($value);
    }

    if (isset($data['station_name'])) {
      $stationName = $data['station_name'];
      $directory = '../../../database/site/dataSubmit';

      $filePathDataReference = $directory . '/' . $referenceStationName . '.json';
      $res = getDataFromJson($filePathDataReference);
      $dataReference = $res['status'] === 'success' ? $res['data'] : [];

      $filePathDataPrevious = $directory . '/' . $stationName . '.json';
      $res = getDataFromJson($filePathDataPrevious);
      $dataPrevious = $res['status'] === 'success' ? $res['data'] : $dataReference;

      // $service = initializeGoogleDriveClient();

      // if (!empty($_FILES)) {
      //   foreach ($_FILES as $fileKey => $fileArray) {
      //     $uploadDir = $stationName . '_files/';
      //     if (!is_dir($uploadDir)) {
      //       mkdir($uploadDir, 0777, true);
      //     }

      //     foreach ($fileArray['name'] as $index => $fileName) {
      //       $fileTmpName = $fileArray['tmp_name'][$index];
      //       $filePath = $uploadDir . basename($fileName);
      //       $folderId = '1Hb0ikxBxcjKKOHwbN09B-zpjLFal-Ch7';

      //       if (move_uploaded_file($fileTmpName, $filePath)) {
      //         $fileNameGGDrive = $stationName . '_' . $fileKey . '_' . $index . '_' . $fileName;
      //         $linkImg = uploadFileToGoogleDrive($service, $filePath, $fileNameGGDrive, $folderId);
      //         if ($linkImg) {
      //           $data[$fileKey][] = $linkImg;
      //           unlink($filePath);
      //         } else {
      //           logEntry('Upload file to Google Drive failed: ' . $fileNameGGDrive);
      //           unlink($filePath);
      //         }
      //       }
      //     }

      //     if (empty($data[$fileKey]) && !empty($dataPrevious[$fileKey])) {
      //       $data[$fileKey] = $dataPrevious[$fileKey];
      //     }
      //     rmdir($uploadDir);
      //   }
      // }

      $data['created_at'] = date('Y-m-d H:i:s');
      $data['updated_at'] = date('Y-m-d H:i:s');
      $data['status'] = 'updated';

      // Read and increment ID
      $idFile = '../../../database/site/id.json';
      $currentYear = date('Y');
      $jsonDataIdSite = file_get_contents($idFile);
      $dataIdSite = json_decode($jsonDataIdSite, true);
      // convert to int
      $dataIdSite[$currentYear]["id"] = (int)$data["id"];
      // save id to file
      file_put_contents($idFile, json_encode($dataIdSite, JSON_PRETTY_PRINT));

      $response = saveDataToJson($data, $directory, $stationName);

      echo json_encode($response);
    } else {
      echo json_encode(['status' => 'fail']);
    }
  } else {
    echo json_encode(['status' => 'fail']);
  }
}

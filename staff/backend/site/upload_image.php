<?php
require 'save_info.php'; // Include Google Drive client initialization

// Disable output buffering to prevent unintended output
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
  $files = $_FILES['files'];
  $folderId = '1Hb0ikxBxcjKKOHwbN09B-zpjLFal-Ch7'; // Replace with your folder ID

  $service = initializeGoogleDriveClient();
  $fileLinks = [];

  foreach ($files['tmp_name'] as $index => $tmpName) {
    $filePath = $tmpName;
    $fileName = basename($files['name'][$index]);

    $fileLink = uploadFileToGoogleDrive($service, $filePath, $fileName, $folderId);
    if ($fileLink) {
      $fileLinks[] = $fileLink;
    }
  }

  if (!empty($fileLinks)) {
    // Clear any unintended output
    ob_clean();
    echo json_encode(['status' => 'success', 'fileLinks' => $fileLinks]);
  } else {
    // Clear any unintended output
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload files']);
  }
} else {
  // Clear any unintended output
  ob_clean();
  echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

// End output buffering
ob_end_flush();

<?php
require 'save_info.php'; // Include Google Drive client initialization
// Disable output buffering to prevent unintended output
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  $fileLinks = $data['fileLinks'] ?? []; // Accept an array of file links

  if (!empty($fileLinks)) {
    $service = initializeGoogleDriveClient();
    $errors = [];

    foreach ($fileLinks as $fileLink) {
      // Extract file ID from the link
      $fileId = preg_replace('/.*\/d\/(.*)\/.*/', '$1', $fileLink);

      try {
        $service->files->delete($fileId);
      } catch (Exception $e) {
        $errors[] = [
          'fileLink' => $fileLink,
          'error' => $e->getMessage()
        ];
      }
    }

    if (empty($errors)) {
      // Clear any unintended output
      ob_clean();
      echo json_encode(['status' => 'success']);
    } else {
      // Clear any unintended output
      ob_clean();
      echo json_encode(['status' => 'partial_success', 'errors' => $errors]);
    }
  } else {
    // Clear any unintended output
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'No file links provided']);
  }
} else {
  // Clear any unintended output
  ob_clean();
  echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

// End output buffering
ob_end_flush();

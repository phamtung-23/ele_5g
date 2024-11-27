<?php
// save_json.php

// Start session if using session-based authentication
session_start();

// Check if the user is logged in (using session or any authentication method)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

// Ensure the directory to store JSON files exists
$directory = realpath(__DIR__ . '/../database/sector');
if ($directory === false) {
    $directory ='../database/sector'; // Use relative path if realpath fails
}
if (!is_dir($directory)) {
    mkdir($directory, 0755, true); // Create the directory if it doesn't exist
}

// Read the request body
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

// Validate the input data
if (!$data || !isset($data['site_name']) || !isset($data['sectors']) || !is_array($data['sectors'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit();
}

// Process each sector and save it as an individual JSON file
try {
    foreach ($data['sectors'] as $sector) {
        // Validate that `sector_name` exists in each sector
        if (!isset($sector['sector_name'])) {
            throw new Exception('Each sector must include a sector_name.');
        }

        // Generate the file name using `sector_name`
        $fileName = $sector['sector_name'] . '.json';
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        // Write the sector data to the JSON file
        file_put_contents($filePath, json_encode($sector, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    echo json_encode(['success' => true, 'message' => 'Sectors saved successfully.']);
} catch (Exception $e) {
    // Handle any errors during file operations
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

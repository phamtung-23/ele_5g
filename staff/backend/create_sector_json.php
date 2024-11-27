<?php
// Start session if using session-based authentication
session_start();

// Check if the user is logged in (using session or any authentication method)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$email = $_SESSION['user_id'];  // Corrected _SESSION to $_SESSION
$inputData = file_get_contents("php://input");
$jsonData = json_decode($inputData, true);

if ($jsonData === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

if (!isset($jsonData['site_name']) || !isset($jsonData['number_of_sector']) || !isset($jsonData['sectors'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required data']);
    exit;
}

// Directory to save sector data
$directory = '../../database/site';  // Changed to "site" directory for email-based storage
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

// Define the email file path (email.json file)
$emailFilePath = $directory . DIRECTORY_SEPARATOR . $email . '.json';

// Prepare data to be added for the current request
$siteData = [
    'email' => $email,
    'time_create' => date('Y-m-d H:i:s'),
    'province' => substr($jsonData['site_name'], 0, 3),  // Extract first 3 characters for province
    'site' => $jsonData['site_name'],
    'number_of_sector' => $jsonData['number_of_sector'],  // Add number_of_sector field
];

// Check if the file already exists
if (file_exists($emailFilePath)) {
    // If file exists, read the current data
    $existingData = json_decode(file_get_contents($emailFilePath), true);
    if ($existingData === null) {
        $existingData = [];
    }
} else {
    // If file does not exist, initialize an empty array
    $existingData = [];
}

// Add the new site data to the existing data
$existingData[] = $siteData;

// Save the updated data back to the email JSON file
if (file_put_contents($emailFilePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to write to file']);
    exit;
}

// Directory for saving individual sector data
$sectorDirectory = '../../database/sector';
if (!is_dir($sectorDirectory)) {
    mkdir($sectorDirectory, 0755, true);
}

// Iterate over each sector and save them as individual JSON files
$saveResults = [];
foreach ($jsonData['sectors'] as $sector) {
    if (!isset($sector['sector_name'])) {
        continue;  // Skip if sector_name is not provided
    }

    // Generate a unique file name for each sector (using sector_name)
    $sectorFileName = $sector['sector_name'] . '.json';
    $sectorFilePath = $sectorDirectory . DIRECTORY_SEPARATOR . $sectorFileName;

    try {
        // Write each sector data to its own JSON file
        if (file_put_contents($sectorFilePath, json_encode($sector, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            throw new Exception("Failed to write to file: $sectorFileName");
        }
        $saveResults[] = [
            'sector_name' => $sector['sector_name'],
            'file_path' => $sectorFilePath,
            'success' => true,
        ];
    } catch (Exception $e) {
        $saveResults[] = [
            'sector_name' => $sector['sector_name'],
            'error' => $e->getMessage(),
            'success' => false,
        ];
    }
}

// Return the result of saving each sector
echo json_encode([
    'success' => true,
    'message' => 'Sector data and email data saved successfully',
    'save_results' => $saveResults,
]);
?>

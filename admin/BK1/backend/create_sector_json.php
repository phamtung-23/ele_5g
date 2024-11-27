<?php
// save_json.php

// Start session if using session-based authentication
session_start();

// Check if the user is logged in (using session or any authentication method)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

// If logged in, continue processing the data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $siteName = $data['site_name'];
    $filePath = "../database/sector/{$siteName}.json";

    // Save the JSON data to the file
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
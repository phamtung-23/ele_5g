<?php
session_name("ele_5g_admin");
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $response = [
        'status' => 'error',
        'message' => 'Unauthorized access'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if it's a POST request with the delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Get the user ID to be deleted
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    
    if (empty($userId)) {
        $response = [
            'status' => 'error',
            'message' => 'User ID is required'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Path to the JSON file containing user data
    $jsonFilePath = "../database/account/users.json";
    
    // Check if the file exists
    if (!file_exists($jsonFilePath)) {
        $response = [
            'status' => 'error',
            'message' => 'User database not found'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Read the current JSON data
    $jsonData = file_get_contents($jsonFilePath);
    $users = json_decode($jsonData, true);
    
    // Find and remove the user with the matching ID
    $userFound = false;
    foreach ($users as $key => $user) {
        if (isset($user['id']) && $user['id'] === $userId) {
            // Remove the user from the array
            unset($users[$key]);
            $userFound = true;
            break;
        }
    }
    
    if (!$userFound) {
        $response = [
            'status' => 'error',
            'message' => 'User not found'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Reindex the array to ensure proper JSON formatting
    $users = array_values($users);
    
    // Write the updated data back to the file
    $result = file_put_contents($jsonFilePath, json_encode($users, JSON_PRETTY_PRINT));
    
    if ($result === false) {
        $response = [
            'status' => 'error',
            'message' => 'Failed to update user database'
        ];
    } else {
        $response = [
            'status' => 'success',
            'message' => 'User deleted successfully'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

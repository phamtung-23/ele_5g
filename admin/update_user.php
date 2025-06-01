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
    exit();
}

// Include helper functions
require_once '../helper/general.php';

// Check if it's a POST request with the update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    // Get user ID and form data
    $userId = isset($_POST['userId']) ? $_POST['userId'] : '';
    
    if (empty($userId)) {
        $response = [
            'status' => 'error',
            'message' => 'User ID is required'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Path to the JSON file containing user data
    $jsonFilePath = "../database/account/users.json";
    
    // Get the existing user information
    $userResult = getUserInfoById($userId, $jsonFilePath);
    
    if ($userResult['status'] === 'fail') {
        $response = [
            'status' => 'error',
            'message' => 'User not found'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Prepare data for update
    $userData = [
        'fullname' => isset($_POST['fullname']) ? $_POST['fullname'] : $userResult['data']['fullname'],
        'email' => isset($_POST['email']) ? $_POST['email'] : $userResult['data']['email'],
        'role' => isset($_POST['role']) ? $_POST['role'] : $userResult['data']['role'],
        'province' => isset($_POST['province']) ? $_POST['province'] : $userResult['data']['province'],
        'phone' => isset($_POST['phone']) ? $_POST['phone'] : $userResult['data']['phone'],
        'idtele' => isset($_POST['idtele']) ? $_POST['idtele'] : $userResult['data']['idtele']
    ];
    
    // Add group if user is a staff
    if ($userData['role'] === 'staff' && isset($_POST['group'])) {
        $userData['group'] = $_POST['group'];
    }
    
    // Update user information
    $updateResult = updateUserInfoById($userId, $userData, $jsonFilePath);
    
    if ($updateResult['status'] === 'success') {
        $response = [
            'status' => 'success',
            'message' => 'User information updated successfully'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Failed to update user information'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

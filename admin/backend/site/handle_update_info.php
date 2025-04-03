<?php
include '../../../helper/general.php'; // Adjust the path as necessary
include '../../../helper/payment.php'; // Adjust the path as necessary

header('Content-Type: application/json');

// Get data from POST request
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if the data is valid
  if (isset($data['userId'])) {
    $userID = $data['userId'];
    $filePath = '../../../database/account/users.json';

    // get the user info by user id
    $res = getUserInfoById($userID, $filePath);

    if ($res['status'] === 'success') {
      $userInfo = $res['data'];
    } else {
      $response = ['status' => 'fail', 'message' => 'User not found'];
    }
    // update the user info
    $userInfo['role'] = $data['role'];
    $userInfo['province'] = $data['province'];

    // update the user info
    $response = updateUserInfoById($userID, $userInfo, $filePath);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail', 'message' => 'User ID not provided']);
  }
} else {
  echo json_encode(['status' => 'fail', 'message' => 'Invalid request method']);
}

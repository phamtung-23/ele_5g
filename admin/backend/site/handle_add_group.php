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
    $group = isset($data['group']) ? $data['group'] : '';
    $filePath = '../../../database/account/users.json';

    // get the user info by user id
    $res = getUserInfoById($userID, $filePath);

    if ($res['status'] === 'success') {
      $userInfo = $res['data'];
    } else {
      $response = ['status' => 'fail', 'message' => 'User not found'];
    }

    // check if the group is empty
    if (empty($group)) {
      $response = ['status' => 'fail', 'message' => 'Group not provided'];
      echo json_encode($response);
      exit;
    }
    // add the group to the user info
    $userInfo['group'] = $group;

    // update the user info
    $response = updateUserInfoById($userID, $userInfo, $filePath);

    echo json_encode($response);
  } else {
    echo json_encode(['status' => 'fail', 'message' => 'User ID not provided']);
  }
} else {
  echo json_encode(['status' => 'fail', 'message' => 'Invalid request method']);
}

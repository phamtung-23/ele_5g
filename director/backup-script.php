<?php
require 'vendor/autoload.php'; // Nếu bạn dùng Composer

// Thiết lập thông tin từ Google API
$client = new Google_Client();
$client->setAuthConfig('google.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);

// Tạo Google Service Drive
$service = new Google_Service_Drive($client);

// Đường dẫn đến file JSON trên host
$filePath = '../database/request_2024.json';
$fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => 'backup_request_2024' . date('Y-m-d_H-i-s') . '.json'
]);

$content = file_get_contents($filePath);

// Tải file lên Google Drive
$file = $service->files->create($fileMetadata, [
    'data' => $content,
    'mimeType' => 'application/json',
    'uploadType' => 'multipart'
]);

echo "File đã được backup thành công: " . $file->id;
?>
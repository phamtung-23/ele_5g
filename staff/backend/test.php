<?php
// Bắt đầu phiên làm việc nếu cần
// Bật hiển thị lỗi chi tiết
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tải Google API Client
require_once 'google_api/vendor/autoload.php';

// Hàm upload file lên Google Drive
// Hàm upload file lên Google Drive vào một thư mục cụ thể
function uploadToGoogleDrive($fileTmp, $fileName, $folderId)
{
    try {
        // Cấu hình Google Client
        $client = new Google_Client();
        $client->setAuthConfig('gdcredentials.json'); // Đường dẫn tới file credentials
        $client->addScope(Google_Service_Drive::DRIVE_FILE);

        $driveService = new Google_Service_Drive($client);

        // Tạo đối tượng file Google Drive
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($fileName);
        $file->setMimeType(mime_content_type($fileTmp));

        // Đặt thư mục đích
        $file->setParents([$folderId]);

        // Đọc nội dung file
        $content = file_get_contents($fileTmp);

        // Upload file vào thư mục
        $createdFile = $driveService->files->create($file, [
            'data' => $content,
            'mimeType' => mime_content_type($fileTmp),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        // Trả về link file trên Google Drive
        return "https://drive.google.com/uc?id=" . $createdFile->id;
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Xử lý khi form được submit
$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $uploadedImages = [];
    $images = $_FILES['images'];

    // ID của thư mục Google Drive bạn muốn tải tệp lên
    $folderId = '1702MmGw3Y5C1WKveLqxqhNUzBTD6e19C'; // Thay thế bằng ID thư mục của bạn

    foreach ($images['tmp_name'] as $key => $tmp_name) {
        $fileName = $images['name'][$key];
        $fileTmp = $images['tmp_name'][$key];

        if (is_uploaded_file($fileTmp)) {
            $uploadedImages[] = uploadToGoogleDrive($fileTmp, $fileName, $folderId);
        }
    }

    $response = "Uploaded files:<br>" . implode('<br>', $uploadedImages);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files to Google Drive</title>
</head>
<body>
    <h2>Upload Files to Google Drive</h2>

    <!-- Form upload files -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="images">Select images to upload:</label>
        <input type="file" name="images[]" id="images" multiple required><br><br>
        <input type="submit" value="Upload">
    </form>

    <div id="response">
        <?= $response ?>
    </div>
</body>
</html>

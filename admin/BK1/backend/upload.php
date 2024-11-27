<?php
// Bật hiển thị lỗi chi tiết
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Bắt đầu phiên làm việc nếu cần
session_start();

// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'login.php';</script>";
    exit();
}

// Folder ID của thư mục Google Drive nơi bạn muốn upload tệp
$folderId = '1702MmGw3Y5C1WKveLqxqhNUzBTD6e19C';  // Thay YOUR_FOLDER_ID bằng ID thư mục bạn muốn upload

// Kiểm tra nếu có tệp được tải lên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadedImages = [];
    
    // Ghi lại tất cả thông tin nhận được vào check.log
    file_put_contents('check.log', "POST Data:\n" . print_r($_POST, true) . "\n", FILE_APPEND);  // Ghi dữ liệu POST vào check.log
    file_put_contents('check.log', "FILES Data:\n" . print_r($_FILES, true) . "\n", FILE_APPEND);  // Ghi dữ liệu FILES vào check.log

    // Lấy sector names từ POST dữ liệu
    $sectorNames = isset($_POST['sector_names']) ? $_POST['sector_names'] : [];
    file_put_contents('check.log', "Sector Names: " . print_r($sectorNames, true) . "\n", FILE_APPEND);  // Ghi lại giá trị sector_names vào log

    // Kiểm tra xem có tệp nào đã được chọn và không có lỗi tải lên
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] == UPLOAD_ERR_OK) {
        $images = $_FILES['images'];

        // Tạo Google Client và Drive Service
        require_once 'google_api/vendor/autoload.php';  // Đảm bảo đường dẫn đúng

        $client = new Google_Client();
        $client->setAuthConfig('gdcredentials.json');  // Đảm bảo file credentials đúng đường dẫn
        $client->addScope(Google_Service_Drive::DRIVE_FILE);
        $driveService = new Google_Service_Drive($client);

        // Duyệt qua từng tệp đã tải lên
        foreach ($images['tmp_name'] as $key => $tmp_name) {
            $file_name = $images['name'][$key];
            $file_tmp = $images['tmp_name'][$key];

            // Lấy tên sector từ mảng sectorNames
            $sectorName = isset($sectorNames[$key]) ? $sectorNames[$key] : 'default_sector';

            // Tạo tên file bằng cách kết hợp sector_name và tên file gốc
            $file_name_with_sector = $sectorName . '_' . $file_name;  // Ví dụ: sector_name_ten_image.jpg

            // Tạo đối tượng Google Drive file
            $file = new Google_Service_Drive_DriveFile();
            $file->setName($file_name_with_sector);
            $file->setMimeType(mime_content_type($file_tmp));

            // Đặt folder ID cho tệp
            $file->setParents([$folderId]);

            // Đọc nội dung tệp
            $content = file_get_contents($file_tmp);

            // Tải tệp lên Google Drive
            try {
                $createdFile = $driveService->files->create($file, [
                    'data' => $content,
                    'mimeType' => mime_content_type($file_tmp),
                    'uploadType' => 'multipart',
                    'fields' => 'id'
                ]);

                // Thêm liên kết vào mảng
                $uploadedImages[] = "https://drive.google.com/uc?id=" . $createdFile->id;
            } catch (Exception $e) {
                // Ghi lỗi vào log và trả lỗi về
                file_put_contents('check.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
                echo json_encode(['success' => false, 'error' => 'Failed to upload to Google Drive: ' . $e->getMessage()]);
                exit();
            }
        }

        // Trả về kết quả thành công với các liên kết tệp
        echo json_encode(['success' => true, 'imageLinks' => $uploadedImages]);
    } else {
        // Nếu không có tệp nào được tải lên hoặc có lỗi
        echo json_encode(['success' => false, 'error' => 'No files uploaded or there was an upload error.']);
    }
} else {
    // Nếu yêu cầu không phải là POST
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>

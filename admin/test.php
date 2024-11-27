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
    <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
        <label for="images">Select images to upload:</label>
        <input type="file" name="images[]" id="images" multiple><br><br>
        <input type="submit" value="Upload">
    </form>

    <div id="response"></div>

    <script>
        // Handle form submission via AJAX (optional, to avoid page reload)
       document.getElementById('uploadForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    
    console.log('Sending data:', formData); // Thêm log dữ liệu gửi đi để kiểm tra
    
    try {
        const response = await fetch('backend/upload.php', { // Đảm bảo đường dẫn đúng
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        console.log('Response:', result); // Thêm log phản hồi từ server

        if (result.success) {
            document.getElementById('response').innerHTML = `Files uploaded successfully! Links: <br>${result.imageLinks.join('<br>')}`;
        } else {
            document.getElementById('response').innerHTML = 'Image upload failed. Error: ' + result.error;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('response').innerHTML = 'An error occurred during the upload process.';
    }
});
    </script>
</body>
</html>

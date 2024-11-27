function createTable() {
    const siteName = document.getElementById("site_name").value.trim();
    const numberOfSector = parseInt(document.getElementById("number_of_sector").value, 10);
    const tableBody = document.querySelector("#inputForm table tbody");
    const formContainer = document.querySelector(".form-container");

    // Check if inputs are valid
    if (!siteName) {
        alert("Please enter a Site Name.");
        return;
    }
    if (!numberOfSector || numberOfSector < 1) {
        alert("Number of Sector must be at least 1.");
        return;
    }

    // Simulate checking if the file exists
    fetch(`../database/sector/${siteName}.json`, { method: "HEAD" })
        .then((response) => {
            if (response.ok) {
                // File exists
                alert(`The site name "${siteName}" already exists.`);
            } else {
                // File does not exist
                alert(`Creating table rows for site "${siteName}" with ${numberOfSector} sectors.`);

                // Clear existing rows
                tableBody.innerHTML = "";

                // Add rows dynamically
                for (let i = 1; i <= numberOfSector; i++) {
                    const row = document.createElement("tr");

                    // Add cells for each column
                    const columns = [
                        "no", "pro", "bat", "sector", "azimuth", "windy_area", "poles",
                        "height_tower", "type_tower", "size_tower", "diameter_column",
                        "ant_8ports", "ant_10ports", "ant_12ports", "aau_mimo",
                        "ant_2ports_18_21", "ant_2ports_900", "ant_4ports_high",
                        "ant_2ports_1800", "ant_4ports", "total_ant",
                        "rru_2g_1800", "rru_2g_900", "rru_4g_850", "rru_4g_1_8",
                        "rru_4g_2_1", "rru_4g_2_6", "rru_db", "rru_trb", "total_rru",
                        "viba_06", "viba_09", "viba_12", "viba_18", "survey_image"
                    ];

                    columns.forEach((col, index) => {
                        const cell = document.createElement("td");

                        // For "No", "Pro", "BAT", and "Sector" columns, add text directly into cells
                        if (col === "no") {
                            cell.textContent = i; // "No" will be the sector number
                        } else if (col === "pro") {
                            cell.textContent = siteName.substring(0, 3).toUpperCase(); // "Pro" will be the first 3 letters of site_name
                        } else if (col === "bat") {
                            cell.textContent = siteName; // "BAT" will be the full site_name
                        } else if (col === "sector") {
                            cell.textContent = `${siteName}_${i}`; // "Sector" will be site_name_No
                        } else if (col === "survey_image") {
                            // For "survey_image", create file input for multiple images
                            const fileInput = document.createElement("input");
                            fileInput.type = "file";
                            fileInput.name = `survey_image_${i}`; // Add unique name for each file input
                            fileInput.multiple = true; // Allow multiple images
                            cell.appendChild(fileInput);
                        } else {
                            // For other columns, make them editable by setting the contenteditable attribute
                            cell.setAttribute("contenteditable", "true");
                        }

                        row.appendChild(cell);
                    });

                    tableBody.appendChild(row);
                }

                // Show the table
                formContainer.classList.remove("hidden");
            }
        })
        .catch((error) => {
            console.error("Error checking site file:", error);
            alert("An error occurred while checking the site name.");
        });
}

document.getElementById("inputForm").addEventListener("submit", async function(event) {
    event.preventDefault();  // Ngừng hành động mặc định của form

    const siteName = document.getElementById("site_name").value.trim();  // Lấy tên site
    const numberOfSector = parseInt(document.getElementById("number_of_sector").value, 10);  // Lấy số lượng sector
    const tableRows = document.querySelectorAll("#inputForm tbody tr");  // Lấy tất cả các dòng trong tbody
    const sectorsData = [];
    const formData = new FormData();  // Tạo FormData để gửi lên server

    tableRows.forEach((row, index) => {
        const sectorData = {};
        const sectorName = `${siteName}_${index + 1}`;  // Tạo tên sector từ site name và index
        
        // Thêm sector name vào FormData
        formData.append('sector_names[]', sectorName);  // Gửi danh sách sector names dưới dạng mảng

        // Lấy dữ liệu từ các cột khác trong bảng
        row.querySelectorAll("td input").forEach((input) => {
            if (input.name !== `survey_image_${index + 1}`) {  // Bỏ qua trường survey_image
                sectorData[input.name] = input.value;
            }
        });

        // Thêm thông tin hình ảnh vào FormData
        const imageInput = row.querySelector(`input[name="survey_image_${index + 1}"]`);
        if (imageInput && imageInput.files.length > 0) {
            for (const file of imageInput.files) {
                formData.append("images[]", file);  // Thêm tệp hình ảnh vào FormData
            }
        }

        sectorsData.push(sectorData);  // Lưu thông tin sector vào mảng sectorsData
    });

    // Thêm thông tin về site và số lượng sector vào FormData
    formData.append("site_name", siteName);
    formData.append("number_of_sector", numberOfSector);
    formData.append("sectors_data", JSON.stringify(sectorsData));  // Chuyển đổi sectorsData thành chuỗi JSON

    // Log tất cả các cặp key-value trong FormData
    formData.forEach((value, key) => {
        console.log(key, value);
    });

    // Kiểm tra tệp tin đã được thêm vào FormData
    const images = formData.getAll('images[]');
    console.log("Images being uploaded:", images);

    try {
        // Gửi tệp tin và dữ liệu sector lên server
        const uploadResponse = await fetch("backend/upload.php", {
            method: "POST",
            body: formData
        });

        const uploadResult = await uploadResponse.json();  // Chuyển đổi phản hồi thành JSON
        
        if (uploadResult.success) {
            const jsonData = {
                site_name: siteName,
                number_of_sector: numberOfSector,
                sectors: sectorsData
            };

            // Gửi dữ liệu sector lên backend để tạo file JSON
            const createResponse = await fetch("backend/create_sector_json.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(jsonData)
            });

            const createResult = await createResponse.json();
            if (createResult.success) {
                alert("Data saved successfully!");  // Thông báo thành công
            } else {
                alert("Error saving sector data.");  // Thông báo lỗi khi lưu dữ liệu
            }
        } else {
            alert("Image upload failed.");  // Thông báo lỗi khi tải ảnh lên
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred during the submission process.");  // Thông báo lỗi tổng quát
    }
});

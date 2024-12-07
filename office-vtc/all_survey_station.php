<?php
session_name("ele_5g_office_vtc");
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'office_vtc') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

require_once '../helper/general.php';
require_once '../helper/getDataFormXlsx.php';

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id'];
$emailJsonPath = "../database/site/email.json";

// get information of the user by email
$userInfoRes = getUserInfo($userEmail);
if ($userInfoRes['status'] !== 'fail') {
    $userInfo = $userInfoRes['data'];
} else {
    echo "<script>alert('Error getting user information!'); window.location.href = 'index.php';</script>";
    exit();
}


$dataFormInit = getDataFormXlsx('../database/template/init_form.xlsx');

// get label list
$labelList = [];
$keyValueList = [];
// loop through dataFormInit to get index
foreach ($dataFormInit as $row) {
    $keyValueList[] = $row[2];
    $labelList[] = $row[1];
}

$imgListKey = [
    'cb_edc_img',
    'cable_size_used_img',
    'cb_power_before_station_img',
    'ac1_unit_type_img',
    'ac2_unit_type_img',
    'dc1_unused_cb_img',
    'dc2_unused_cb_img',
    'dc3_unused_cb_img',
    'space_for_additional_dc_power_img',
    'can_install_outdoor_units_img',
    'outdoor_battery_rack_model_img',
    'number_of_batteries_in_rack_img',
    'engine_generator_in_use_img',
    'generator_type_in_use_img',
    'number_of_19_racks_img',
    'has_space_for_additional_4g_bbu_img',
    'has_space_for_additional_19_racks_img',
    'number_of_dcdu_at_station_img',
    'has_space_for_additional_dcdu_img'
];

// add label for email on labelList position 2
array_splice($labelList, 7, 0, 'Email');
array_splice($labelList, 9, 0, 'CB EDC Images');
array_splice($labelList, 13, 0, 'Cable Size Used Images');
array_splice($labelList, 16, 0, 'CB Power Before Entering Station Images');
array_splice($labelList, 22, 0, 'AC1_Unit Type Images');
array_splice($labelList, 25, 0, 'AC2_Unit Type Images');
array_splice($labelList, 29, 0, 'DC1_Number of Unused CB (> C63) in DC Units Images');
array_splice($labelList, 34, 0, 'DC2_Number of Unused CB (> C63) in DC Units Images');
array_splice($labelList, 39, 0, 'DC3_Number of Unused CB (> C63) in DC Units Images');
array_splice($labelList, 44, 0, 'If CB Cannot Be Installed, Is There Space for Additional DC Power? - Images');
array_splice($labelList, 47, 0, 'Can the Station Install Additional Outdoor Units? - Images');
array_splice($labelList, 50, 0, 'Outdoor Battery Rack Model Images');
array_splice($labelList, 52, 0, 'Number of Batteries in Battery Rack Images');
array_splice($labelList, 56, 0, 'Engine Generator, In Use - Images');
array_splice($labelList, 61, 0, 'Generator Type in Use - Images');
array_splice($labelList, 77, 0, 'Number of 19" racks - Images');
array_splice($labelList, 79, 0, 'Is there space for additional 4G BBU installation? - Images');
array_splice($labelList, 82, 0, 'Is there space for additional 19" racks? - Images');
array_splice($labelList, 86, 0, 'Number of DCDUs available at the station - Images');
array_splice($labelList, 88, 0, 'Is there space for installing additional DCDU? - Images');
// add key for email on keyList position 2
array_splice($keyValueList, 7, 0, 'email');
array_splice($keyValueList, 9, 0, 'cb_edc_img');
array_splice($keyValueList, 13, 0, 'cable_size_used_img');
array_splice($keyValueList, 16, 0, 'cb_power_before_station_img');
array_splice($keyValueList, 22, 0, 'ac1_unit_type_img');
array_splice($keyValueList, 25, 0, 'ac2_unit_type_img');
array_splice($keyValueList, 29, 0, 'dc1_unused_cb_img');
array_splice($keyValueList, 34, 0, 'dc2_unused_cb_img');
array_splice($keyValueList, 39, 0, 'dc3_unused_cb_img');
array_splice($keyValueList, 44, 0, 'space_for_additional_dc_power_img');
array_splice($keyValueList, 47, 0, 'can_install_outdoor_units_img');
array_splice($keyValueList, 50, 0, 'outdoor_battery_rack_model_img');
array_splice($keyValueList, 52, 0, 'number_of_batteries_in_rack_img');
array_splice($keyValueList, 56, 0, 'engine_generator_in_use_img');
array_splice($keyValueList, 61, 0, 'generator_type_in_use_img');
array_splice($keyValueList, 77, 0, 'number_of_19_racks_img');
array_splice($keyValueList, 79, 0, 'has_space_for_additional_4g_bbu_img');
array_splice($keyValueList, 82, 0, 'has_space_for_additional_19_racks_img');
array_splice($keyValueList, 86, 0, 'number_of_dcdu_at_station_img');
array_splice($keyValueList, 88, 0, 'has_space_for_additional_dcdu_img');

// get all data file json from folder
$directory = '../database/site/dataSubmit';
$filesRes = getAllDataFiles($directory);
$dataFiles = $filesRes['data'];

// filter data by status
$dataFiles = array_filter($dataFiles, function ($item) use ($userEmail, $userInfo) {
    // check $item['approval'] is exist
    if (isset($item['approval'])) {
        return $item['approval'][2]['status'] === 'approved';
    }
});

echo "<script>";
echo "let headers = " . json_encode($labelList) . ";"; // Convert the PHP array to a JavaScript object
echo "let headersKey = " . json_encode($keyValueList) . ";"; // Convert the PHP array to a JavaScript object
echo "let dataFiles = " . json_encode($dataFiles) . ";"; // Convert the PHP array to a JavaScript object
echo "</script>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Head ELE VTC's Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        /* Basic styles for layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 8px 20px 5px 20px;
            text-align: center;
        }

        .header h1{
            font-size: 2em;
            font-weight: bold;
        }

        .menu {
            background-color: #333;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .icon {
            padding: 10px 20px;
        }

        .menu-icon {
            width: 60px;
            height: 40px;
        }

        .menu a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 17px;
        }

        .menu a:hover {
            background-color: #575757;
        }

        .container-table {
            padding: 20px;
        }

        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .menu a.logout {
            float: right;
            background-color: #f44336;
        }

        .menu a.logout:hover {
            background-color: #d32f2f;
        }

        .content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .dataTables_length {
            padding-bottom: 10px;
        }

        .dataTables_filter {
            padding-bottom: 10px;
        }

        /* .form-container {
            width: 100%;
            overflow-x: auto;
        }

        
        input[type="text"], input[type="datetime-local"], select, textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
 */
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #888;
        }

        /* Hamburger icon (hidden by default) */
        .hamburger {
            display: none;
            float: right;
            font-size: 28px;
            cursor: pointer;
            color: white;
            padding: 10px 20px;
        }

        /* Basic responsive adjustments */
        @media (max-width: 950px) {

            /* Header and menu adjustments */
            .header {
                padding: 20px;
                font-size: 1.5em;
            }

            .header h1 {
                font-size: 1.2em;
            }

            .menu {
                background-color: #333;
                overflow: hidden;
                display: block;
            }

            .menu a {
                float: none;
                display: block;
                text-align: left;
                padding: 10px;
            }

            .menu a.logout {
                float: none;
                background-color: #f44336;
                text-align: center;
            }

            /* Container adjustments */
            .container-table {
                padding: 10px;
            }

            .welcome-message {
                font-size: 18px;
                text-align: center;
            }

            /* Content adjustments */
            .content {
                padding: 10px;
                margin-top: 15px;
            }

            .menu a {
                display: none;
            }

            .menu a.logout {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .menu.responsive a {
                float: none;
                display: block;
                text-align: left;
            }

            .menu.responsive .logout {
                float: none;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.2em;
            }

            .menu a {
                font-size: 0.9em;
            }

            .welcome-message {
                font-size: 16px;
            }

            table,
            th,
            td {
                font-size: 0.9em;
                padding: 6px;
            }

            .content h2 {
                font-size: 1em;
            }

            .footer {
                font-size: 12px;
            }

            .menu a {
                display: none;
            }

            .menu a.logout {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .menu.responsive a {
                float: none;
                display: block;
                text-align: left;
            }

            .menu.responsive .logout {
                float: none;
            }
        }

        .submit-btn {
            font-size: 1.2em;
            /* Tăng kích thước chữ lên 150% */
            padding: 16px 32px;
            /* Tăng khoảng cách xung quanh chữ */
            border-radius: 8px;
            /* Thêm bo góc cho nút */
            background-color: #4CAF50;
            /* Màu nền */
            color: white;
            /* Màu chữ */
            border: none;
            /* Không có viền */
            cursor: pointer;
            /* Thêm hiệu ứng con trỏ khi hover */
            transition: background-color 0.3s ease;
            /* Thêm hiệu ứng khi hover */
        }

        .submit-btn:hover {
            background-color: #45a049;
            /* Thay đổi màu nền khi hover */
        }

        .hidden {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            /* Allow words to break and avoid overflow */
        }

        table th {
            background-color: #f2f2f2;
            text-align: center;
            vertical-align: bottom;

            padding: 10px;

        }

        table td {
            word-wrap: break-word;
            /* Make text wrap instead of overflow */
            overflow-wrap: break-word;
            /* Make sure long words break to fit in cells */
            min-width: 45px;
            /* Set minimum width for the first few columns */
        }

        /* Add specific column width adjustments for columns 2, 3, and 4 (PRO, BAT, Sector) */
        table td:nth-child(2),
        table td:nth-child(3),
        table td:nth-child(4) {
            min-width: 100px;
            /* Increase the width for these columns */
        }

        /* Apply background color to header columns */

        /* Column color for 1 to 11 (Green) */
        table th:nth-child(n+1):nth-child(-n+11) {
            background-color: #98FB98;
            /* Light green */
        }

        /* Column color for 12 to 21 (Yellow) */
        table th:nth-child(n+12):nth-child(-n+21) {
            background-color: #FFFF00;
            /* Yellow */
        }

        /* Column color for 22 to 30 (Dark Green) */
        table th:nth-child(n+22):nth-child(-n+30) {
            background-color: #006400;
            /* Dark green */
            color: white;
            /* Make text white for better contrast */
        }

        /* Column color for 31 to 38 (Orange) */
        table th:nth-child(n+31):nth-child(-n+38) {
            background-color: #FFA500;
            /* Orange */
            color: white;
            /* Make text white for better contrast */
        }
    </style>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="header">
        <h1>Head ELE VTC's Dashboard</h1>
    </div>


    <div class="menu">
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="icon">
            <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
        </div>
        <a href="index.php">Home</a>
        <a href="all_site.php">Station Managment</a>
        <a href="all_survey_station.php">Survey Station Managment</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container-table">
        <div class="welcome-message">
            <p>Welcome, <?php echo $fullName; ?>!</p>
        </div>

        <div class="content">
            <div class="d-flex justify-content-end m-2">
                <button class="btn btn-success" onclick="exportToCSV()">
                    <i class="ph ph-export"></i>
                    Export CSV
                </button>
            </div>
            <!-- Data Table -->
            <table id="dataTable">
                <thead>
                    <tr>
                        <?php
                        foreach ($labelList as $label) {
                            echo "<th class='align-middle'>" . htmlspecialchars($label) . "</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataFiles as $item) {
                        echo "<tr>";
                        // loop through labelList to get value
                        foreach ($keyValueList as $key) {
                            if (isset($item[$key])) {
                                // check if the key is in the imgListKey array
                                if (array_search($key, $imgListKey) !== false) {
                                    echo "<td>";
                                    foreach ($item[$key] as $img) {
                                        echo "<a href='" . $img . "' target='_blank'>View Image</a><br/>";
                                    }
                                    echo "</td>";
                                } else {
                                    echo "<td>" . htmlspecialchars($item[$key]) . "</td>";
                                }
                            } else {
                                echo "<td></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Initialize DataTable with search, pagination, and other features
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "scrollX": true
            });
        });

        // Update functionality (for example, open a modal to update the record)
        function updateRecord(id) {
            // redirect to the update page
            window.location.href = 'update_site.php?station_name=' + id;
        }
        // Toggle the responsive class to show/hide the menu
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('responsive');
        }

        function exportToCSV() {
            let filename = 'survey_data.csv';

            Swal.fire({
                title: "Exporting...!",
                html: "Please wait for a moment.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            });
            // Build the CSV rows
            const csvRows = [];
            csvRows.push(headers.join(',')); // Add the headers row

            // Add data rows
            for (const data of dataFiles) {
                const values = headersKey.map(key => {
                    if (data[key] === null || data[key] === undefined) {
                        return ''; // Handle null/undefined
                    } else if (Array.isArray(data[key])) {
                        // Convert array to a string separated by a newline within the cell
                        return `"${data[key].join('\n \n')}"`; // Wrap in quotes to handle CSV format
                    } else {
                        return `"${data[key]}"`; // Wrap other values in quotes
                    }
                });

                csvRows.push(values.join(',')); // Join the row with commas
            }

            // Combine rows into a CSV string
            const csvString = csvRows.join('\n');

            // Create a Blob and trigger the download
            const blob = new Blob([csvString], {
                type: 'text/csv'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            // close the loading dialog
            Swal.close();
        }
    </script>
    <div class="footer">
        <p>© 2024 Metfone 5G survey software developed by Hienlm 0988838487</p>
    </div>
</body>

</html>
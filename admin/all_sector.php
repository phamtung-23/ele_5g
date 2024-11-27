<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}
$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id']; 

$sectorData = [];
$sectorDirectory = "../database/sector/";

if (is_dir($sectorDirectory)) {
    $files = scandir($sectorDirectory); // Get all files in the sector directory

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json') { // Only check JSON files
            // Load the data from the sector JSON file
            $sectorJsonPath = $sectorDirectory . $file;
            $sectorJsonData = file_get_contents($sectorJsonPath);
            if ($sectorJsonData === false) {
                die("Error reading sector file: {$file}");
            }
            $sectorData[] = json_decode($sectorJsonData, true); // Add sector data to the array
        }
    }
} else {
    die('Sector directory does not exist.');
}

// Export to CSV function
if (isset($_POST['export'])) {
    exportToCSV($sectorData);
}

function exportToCSV($data) {
    $filename = "sector_data_" . date("Y-m-d_H-i-s") . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');

    // Get column headers
    $headers = ["No", "Province", "Site", "Sector", "Azimuth", "Windy Area", "Poles Roof Ground", "Tower Height", "Tower Type", "Tower Size", "Tube Diameter", "Ant 8 Ports Height", "Ant 10 Ports Height", "Ant 12 Ports Height", "AAU MIMO Height", "Ant 2 Ports 1.8 2.1 Height", "Ant 2 Ports 900 Height", "Ant 4 Ports High Gain Height", "Ant 2 Ports 1800 Height", "Ant 4 Ports Height", "Total Ant", "RRU 2G 1800 Height", "RRU 2G 900 Height", "RRU 4G 850 Height", "RRU 4G 1.8 Height", "RRU 4G 2.1 Height", "RRU 4G 2.6 Height", "RRU DB Height", "RRU TRB Height", "Total RRU", "Viba D 0.6 Height", "Viba D 0.9 Height", "Viba D 1.2 Height", "Viba D 1.8 Height", "Image Links", "Action"];
    fputcsv($output, $headers);

    // Add data rows
    $index = 1;
    foreach ($data as $sector) {
        if (is_array($sector)) {
            $sectorDataSlice = array_slice($sector, 2); // Skip the first two indices
            $row = [$index++];
            foreach ($sectorDataSlice as $value) {
                if (is_array($value)) {
                    $links = implode(" ", array_map(function($link) {
                        return htmlspecialchars($link);
                    }, $value));
                    $row[] = $links;
                } else {
                    $row[] = htmlspecialchars($value);
                }
            }
            // Add action column
            $row[] = 'Update';
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin's Dashboard</title>
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
            padding: 10px 20px;
            text-align: center;
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

        .container {
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

        .form-container {
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
            .container {
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
    font-size: 1.2em; /* Tăng kích thước chữ lên 150% */
    padding: 16px 32px; /* Tăng khoảng cách xung quanh chữ */
    border-radius: 8px; /* Thêm bo góc cho nút */
    background-color: #4CAF50; /* Màu nền */
    color: white; /* Màu chữ */
    border: none; /* Không có viền */
    cursor: pointer; /* Thêm hiệu ứng con trỏ khi hover */
    transition: background-color 0.3s ease; /* Thêm hiệu ứng khi hover */
}

.submit-btn:hover {
    background-color: #45a049; /* Thay đổi màu nền khi hover */
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

table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    word-wrap: break-word; /* Allow words to break and avoid overflow */
}

table th {
    background-color: #f2f2f2;
    text-align: center;
    vertical-align: bottom;
    writing-mode: vertical-rl; /* Rotate the text vertically */
    transform: rotate(0deg);
    padding: 10px;
    height: 150px; /* Make header taller */
}

table td {
    word-wrap: break-word; /* Make text wrap instead of overflow */
    overflow-wrap: break-word; /* Make sure long words break to fit in cells */
    min-width: 45px; /* Set minimum width for the first few columns */
}0

/* Add specific column width adjustments for columns 2, 3, and 4 (PRO, BAT, Sector) */
table td:nth-child(2),
table td:nth-child(3),
table td:nth-child(4) {
    min-width: 100px; /* Increase the width for these columns */
}
/* Apply background color to header columns */

/* Column color for 1 to 11 (Green) */
table th:nth-child(n+1):nth-child(-n+11) {
    background-color: #98FB98; /* Light green */
}

/* Column color for 12 to 21 (Yellow) */
table th:nth-child(n+12):nth-child(-n+21) {
    background-color: #FFFF00; /* Yellow */
}

/* Column color for 22 to 30 (Dark Green) */
table th:nth-child(n+22):nth-child(-n+30) {
    background-color: #006400; /* Dark green */
    color: white; /* Make text white for better contrast */
}

/* Column color for 31 to 38 (Orange) */
table th:nth-child(n+31):nth-child(-n+38) {
    background-color: #FFA500; /* Orange */
    color: white; /* Make text white for better contrast */
}

    </style>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="header">
    <h1>All Sectors</h1>
</div>

<div class="menu">
    <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
    <div class="icon">
        <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
    </div>
    <a href="index.php">Home</a>
    <a href="all_sector.php">Sectors Management</a>
    <a href="acc_management.php">Account Management</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="container">
    <div class="welcome-message">
        <p>Welcome, <?php echo htmlspecialchars($fullName); ?>!</p>
    </div>

    <div class="content">
        <form method="POST">
            <button type="submit" name="export" class="submit-btn">Export to CSV</button>
        </form>
        <table id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Province</th>
                    <th>Site</th>
                    <th>Sector</th>
                    <th>Azimuth</th>
                    <th>Windy Area</th>
                    <th>Poles Roof Ground</th>
                    <th>Tower Height</th>
                    <th>Tower Type</th>
                    <th>Tower Size</th>
                    <th>Tube Diameter</th>
                    <th>Ant 8 Ports Height</th>
                    <th>Ant 10 Ports Height</th>
                    <th>Ant 12 Ports Height</th>
                    <th>AAU MIMO Height</th>
                    <th>Ant 2 Ports 1.8 2.1 Height</th>
                    <th>Ant 2 Ports 900 Height</th>
                    <th>Ant 4 Ports High Gain Height</th>
                    <th>Ant 2 Ports 1800 Height</th>
                    <th>Ant 4 Ports Height</th>
                    <th>Total Ant</th>
                    <th>RRU 2G 1800 Height</th>
                    <th>RRU 2G 900 Height</th>
                    <th>RRU 4G 850 Height</th>
                    <th>RRU 4G 1.8 Height</th>
                    <th>RRU 4G 2.1 Height</th>
                    <th>RRU 4G 2.6 Height</th>
                    <th>RRU DB Height</th>
                    <th>RRU TRB Height</th>
                    <th>Total RRU</th>
                    <th>Viba D 0.6 Height</th>
                    <th>Viba D 0.9 Height</th>
                    <th>Viba D 1.2 Height</th>
                    <th>Viba D 1.8 Height</th>
                    <th>Image Links</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $index = 1; // Initialize the index variable
                foreach ($sectorData as $sector) {
                    if (is_array($sector)) {
                        $sectorDataSlice = array_slice($sector, 2); // Skip first two indices
                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        foreach ($sectorDataSlice as $value) {
                            if (is_array($value)) {
                                echo "<td>";
                                foreach ($value as $i => $link) {
                                    echo "<a href='" . htmlspecialchars($link) . "' target='_blank'>" . ($i + 1) . "</a> "; 
                                }
                                echo "</td>";
                            } else {
                                echo "<td>" . htmlspecialchars($value) . "</td>";
                            }
                        }
                        echo "<td><button onclick='window.location.href=\"update_page.php?id=" . $sector['sector_name'] . "\"'>Update</button></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='38'>Invalid data in sector.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Telecommunications Network Operations & Technical Center</p>
</div>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "responsive": true
        });
    });
</script>

<script>
    function toggleMenu() {
        var menu = document.querySelector('.menu');
        menu.classList.toggle('responsive');
    }
</script>

</body>
</html>

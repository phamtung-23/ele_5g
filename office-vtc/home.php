<?php
session_name("ele_5g_office_vtc");
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'office_vtc') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

require_once '../helper/general.php';

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id']; 
$emailJsonPath = "../database/site/email.json";

// Check if the file exists and load its data
$data = [];
if (file_exists($emailJsonPath)) {
    $jsonData = file_get_contents($emailJsonPath);
    $data = json_decode($jsonData, true); // Decode the JSON into an array
}

echo "<script>";
echo "let data = " . json_encode($data) . ";"; // Convert the PHP array to a JavaScript object
echo "console.log(data);"; // Log the data to the console
echo "</script>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Head ELE VTC's Dashboard</title>
    
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

    padding: 10px;

}

table td {
    word-wrap: break-word; /* Make text wrap instead of overflow */
    overflow-wrap: break-word; /* Make sure long words break to fit in cells */
    min-width: 45px; /* Set minimum width for the first few columns */
}

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
         <a href="all_site.php">Station Management</a>
         <a href="all_survey_station.php">Survey Station Managment</a>
        <!-- <a href="create_site.php">Survey Station</a> -->
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <div class="welcome-message">
            <p>Welcome, <?php echo $fullName; ?>!</p>
        </div>

        <div class="content">
          
            
            <!-- Data Table -->
            <table id="dataTable">
                <caption style="font-size: 1.5em; font-weight: bold; margin-bottom: 10px;">Pending Approval List</caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Email</th>
                        <th>Province</th>
                        <th>Site</th>
                        <th>Status</th>
                        <th>Update time</th>
                        <th>Pending approve level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php 
                        $no = 1; // Initialize the counter variable for No
                        foreach ($data as $key => $value): 
                            $itemEmail = $key;
                            foreach ($value as $itemKey => $itemValue):
                                $stationType = $itemKey;
                                $stationNumber = $itemValue['number'];
                                $stationList = $itemValue['list'];
                                $stationData = [];
                                $approvalStatus = [];
                                foreach ($stationList as $stationId):
                                    $res = getDataByStationName($stationId);
                                    if ($res['status'] === 'success') {
                                        $stationData = $res['data'];
                                    } else {
                                        $stationData = [];
                                    }

                                    // get approval status by current role
                                    if (!empty($stationData)) {
                                        $approvalStatus = getApprovalStatusByRole($stationData['approval'], $_SESSION['role']);
                                    } else {
                                        $approvalStatus = [];
                                    }
                                    // get approval status by bod_pro_gis role
                                    if (!empty($stationData)) {
                                        $approvalStatusBodProGis = getApprovalStatusByRole($stationData['approval'], 'bod_pro_gis');
                                    } else {
                                        $approvalStatusBodProGis = [];
                                    }
                                    // get approval status by office_gis role
                                    if (!empty($stationData)) {
                                        $approvalStatusOfficeGis = getApprovalStatusByRole($stationData['approval'], 'office_gis');
                                    } else {
                                        $approvalStatusOfficeGis = [];
                                    }

                                    $status = empty($approvalStatus) ? '': htmlspecialchars($approvalStatus['status']);
                                    $statusBodProGis = empty($approvalStatusBodProGis) ? '': htmlspecialchars($approvalStatusBodProGis['status']);
                                    $statusOfficeGis = empty($approvalStatusOfficeGis) ? '': htmlspecialchars($approvalStatusOfficeGis['status']);

                                    if ($status !== 'pending' || $statusBodProGis !== 'approved' || $statusOfficeGis !== 'approved') {
                                        continue;
                                    }

                                    $colors = [ 'pending' => '#FF8C00', 'rejected' => 'red', 'approved' => 'green', '' => 'black' ];
                                    $colorText = $colors[$status];
                        ?>
                                <tr>
                                    <td><?php echo $no++; ?></td> <!-- Incremented value for "No" -->
                                    <td><?php echo $itemEmail ?></td>
                                    <td><?php echo htmlspecialchars($stationType ); ?></td>
                                    <td><?php echo htmlspecialchars($stationId); ?></td>
                                    <td style="color: <?=$colorText?>;"><?php echo empty($approvalStatus) ? '': htmlspecialchars($approvalStatus['status']); ?></td>
                                    <td><?php echo empty($approvalStatus) ? '': htmlspecialchars($approvalStatus['updateTime']); ?></td>
                                    <td><?php echo empty($approvalStatus) ? '': htmlspecialchars($approvalStatus['role']); ?></td>
                                    <td style="display: flex; justify-content: center;">
                                        <?php 
                                            if ($status !== 'Approved') {
                                                echo '<button class="action-button" style="padding: 5px;" onclick="handleViewDetail(\'' . $stationId . '\')">Detail</button>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No data found</td></tr>
                    <?php endif; ?>
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
                "info": true
            });
        });

        // Update functionality (for example, open a modal to update the record)
        function handleViewDetail(id) {
            // redirect to the update page
            window.location.href = 'update_site.php?station_name=' + id;
        }
        // Toggle the responsive class to show/hide the menu
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('responsive');
        }
    </script>
<div class="footer">
       <p>© 2024 Metfone 5G survey software developed by Hienlm 0988838487</p>
</div>
</body>
</html>

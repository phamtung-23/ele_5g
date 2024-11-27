<?php
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id']; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sector</title>
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
    
</head>

<body>

    <div class="header">
        <h1>Create Sector</h1>
    </div>

    <div class="menu">
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="icon">
            <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
        </div>
      <a href="index.php">Home</a>
        <a href="create_sector.php">Create Sectors</a>
        <a href="all_sector.php">Sectors Managment</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

     <div class="container">
        <div class="welcome-message">
            <p>Welcome, <?php echo $fullName; ?>!</p>
        </div>
    <div style="margin-bottom: 20px;">
    <label for="site_name">Site Name:</label>
    <input type="text" id="site_name" name="site_name" placeholder="Enter Site Name">

 <label for="number_of_sector">Number of Sector:</label>
<input 
    type="number" 
    id="number_of_sector" 
    name="number_of_sector" 
    min="1" 
    value="3">

<button id="create_button" onclick="createTable()">Create</button>
</div>
<div class="form-container hidden">
    <form id="inputForm" enctype="multipart/form-data">
        <table>
            <thead>
                <tr>
                   <th data-column="no">No</th>
                    <th data-column="pro">PRO</th>
                    <th data-column="bat">BAT</th>
                    <th data-column="sector">Sector</th>
                    <th data-column="azimuth">Azimuth</th>
                    <th data-column="windy_area">Windy Area</th>
                    <th data-column="poles_roof_ground">Poles on the roof or on the ground</th>
                    <th data-column="tower_height">Height of Tower (m)</th>
                    <th data-column="tower_type">Type of Tower</th>
                    <th data-column="tower_size">Size of Tower</th>
                    <th data-column="tube_diameter">Column body tube diameter</th>
                    <th data-column="ant_8ports_height">Height ANT 8Ports</th>
                    <th data-column="ant_10ports_height">Height ANT 10Ports</th>
                    <th data-column="ant_12ports_height">Height ANT 12Ports</th>
                    <th data-column="aau_mimo_height">Height AAU Massive MIMO</th>
                    <th data-column="ant_2ports_1_8_2_1_height">Height ANT 2Ports (1.8-2.1)</th>
                    <th data-column="ant_2ports_900_height">Height ANT 2Ports (900)</th>
                    <th data-column="ant_4ports_high_gain_height">Height ANT 4Ports High Gain</th>
                    <th data-column="ant_2ports_1800_height">Height ANT 2Ports (1800)</th>
                    <th data-column="ant_4ports_height">Height ANT 4Ports</th>
                    <th data-column="total_ant">Total ANT</th>
                    <th data-column="rru_2g_1800_height">Height RRU 2G 1800</th>
                    <th data-column="rru_2g_900_height">Height RRU 2G 900</th>
                    <th data-column="rru_4g_850_height">Height RRU 4G 850</th>
                    <th data-column="rru_4g_1_8_height">Height RRU 4G 1.8</th>
                    <th data-column="rru_4g_2_1_height">Height RRU 4G 2.1</th>
                    <th data-column="rru_4g_2_6_height">Height RRU 4G 2.6</th>
                    <th data-column="rru_db_height">Height RRU DB</th>
                    <th data-column="rru_trb_height">Height RRU TrB</th>
                    <th data-column="total_rru">Total RRU</th>
                    <th data-column="viba_d_0_6_height">Height Viba D=0.6</th>
                    <th data-column="viba_d_0_9_height">Height Viba D=0.9</th>
                    <th data-column="viba_d_1_2_height">Height Viba D=1.2</th>
                    <th data-column="viba_d_1_8_height">Height Viba D=1.8</th>
                    <th data-column="survey_image">Survey Image</th> <!-- New column for multiple images -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="no"></td>
                    <td><input type="text" name="pro"></td>
                    <td><input type="text" name="bat"></td>
                    <td><input type="text" name="sector"></td>
                    <td><input type="text" name="azimuth"></td>
                    <td><input type="text" name="windy_area"></td>
                    <td><input type="text" name="poles"></td>
                    <td><input type="text" name="height_tower"></td>
                    <td><input type="text" name="type_tower"></td>
                    <td><input type="text" name="size_tower"></td>
                    <td><input type="text" name="diameter_column"></td>
                    <td><input type="text" name="ant_8ports"></td>
                    <td><input type="text" name="ant_10ports"></td>
                    <td><input type="text" name="ant_12ports"></td>
                    <td><input type="text" name="aau_mimo"></td>
                    <td><input type="text" name="ant_2ports_18_21"></td>
                    <td><input type="text" name="ant_2ports_900"></td>
                    <td><input type="text" name="ant_4ports_high"></td>
                    <td><input type="text" name="ant_2ports_1800"></td>
                    <td><input type="text" name="ant_4ports"></td>
                    <td><input type="text" name="total_ant"></td>
                    <td><input type="text" name="rru_2g_1800"></td>
                    <td><input type="text" name="rru_2g_900"></td>
                    <td><input type="text" name="rru_4g_850"></td>
                    <td><input type="text" name="rru_4g_1_8"></td>
                    <td><input type="text" name="rru_4g_2_1"></td>
                    <td><input type="text" name="rru_4g_2_6"></td>
                    <td><input type="text" name="rru_db"></td>
                    <td><input type="text" name="rru_trb"></td>
                    <td><input type="text" name="total_rru"></td>
                    <td><input type="text" name="viba_06"></td>
                    <td><input type="text" name="viba_09"></td>
                    <td><input type="text" name="viba_12"></td>
                    <td><input type="text" name="viba_18"></td>
                    <td><input type="file" name="survey_image" multiple></td> <!-- Input for multiple image uploads -->
                </tr>
            </tbody>
        </table>
        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>
<div class="footer">
       <p>© 2024 Metfone 5G survey software developed by Hienlm 0988838487</p>
</div>


    <script src="create_sector.js"></script>
    <script>
        // Toggle the responsive class to show/hide the menu
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('responsive');
        }
    </script>
</body>


</html>

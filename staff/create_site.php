<?php
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

include '../helper/getDataFormXlsx.php';
include '../helper/general.php';

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id'];
$emailJsonPath = "../database/site/{$userEmail}.json";

// get data form init_form.xlsx
$dataFormInit = getDataFormXlsx('../database/template/init_form.xlsx');

// Handle the form submission
$showForm = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the station name and reference station from the POST data
    $stationName = $_POST['station_name'];
    $referenceStation = $_POST['reference_station'];

    // Example usage of getDataFromJson
    $filePath = '../database/site/save/' . $stationName . '.json';
    $dataResponse = getDataFromJson($filePath);
    if ($dataResponse['status'] === 'success') {
        $dataSaveInfo = $dataResponse['data'];
        // Do something with the data
    } else {
        $dataSaveInfo = [];
    }
    // Set the flag to show the dynamic form below
    $showForm = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff's Dashboard</title>

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


        input[type="text"],
        input[type="datetime-local"],
        select,
        textarea {
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
            font-size: 1.2em;
            /* Tăng kích thước chữ lên 150% */
            padding: 10px 20px;
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

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            display: flex;
            /* Align items on the same line */
            align-items: center;
            /* Vertically align items in the center */
            margin-bottom: 20px;
        }

        .form-group label {
            flex: 1;
            /* Allow the label to take available space */
            margin-right: 10px;
            /* Space between label and input */
        }

        .form-group .note-icon {
            margin-left: 5px;
            font-size: 1.2em;
            cursor: pointer;
        }

        .form-group input[type="text"],
        .form-group select {
            flex: 3;
            /* Allow the input field to take more space */
            padding: 8px;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        .note {
            background-color: #e9ecef;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
            /* Hide the note by default */
        }
    </style>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="header">
        <h1>Survey Site</h1>
    </div>

    <div class="menu">
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="icon">
            <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
        </div>
        <a href="index.php">Home</a>
        <a href="all_sector.php">Station Managment</a>
        <a href="create_site.php">Survey Station</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <div class="welcome-message">
            <p>Welcome, <?php echo $fullName; ?>!</p>
        </div>

        <!-- Form to submit station name and reference station -->
        <form method="POST">
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="station_name">Station Name:</label>
                </div>
                <div class="col-md-6">
                    <input type="text" id="station_name" name="station_name" value="<?php echo isset($_POST['station_name']) ? htmlspecialchars($_POST['station_name']) : ''; ?>" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="reference_station">Reference Station:</label>
                </div>
                <div class="col-md-6">
                    <input type="text" id="reference_station" name="reference_station" value="<?php echo isset($_POST['reference_station']) ? htmlspecialchars($_POST['reference_station']) : ''; ?>">
                </div>
            </div>

            <div class="form-group d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Create</button>
            </div>
        </form>
        <hr>

        <!-- Displaying form elements from input_ele.json only after the form is submitted -->
        <?php

        if (!empty($dataFormInit) && $showForm) {
            echo '<form id="form-info">';

            $index = 0;

            foreach ($dataFormInit as $row) {
                // Generate the field dynamically
                echo '<div class="form-group row">';
                echo '<div class="col-md-6">
                        <label tabindex="0" role="button" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus" data-bs-content="' . $row['0'] . '">' . $row['1'] . ':</label>
                    </div>';

                // Check if the note is not empty before displaying the icon
                echo '<div class="col-md-6 d-flex flex-column">';
                echo '<div class="d-flex justify-content-start align-items-center">';

                if (!empty($row['6'])) {
                    echo '<span class="note-icon"  tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="' . $row['6'] . '">ℹ️</span>';
                }

                // If there are options, create a select dropdown
                if (!empty($row['5'])) {
                    // Split the options by newline
                    $options = explode("\n", $row['5']);
                    echo '<select class="form-select" id="' . $row['2'] . '" name="' . $row['2'] . '">';
                    echo '<option value="" disabled selected>Choose</option>'; // Placeholder option
                    foreach ($options as $option) {
                        // Split each option by the colon to get the value and name
                        list($value, $name) = explode(': ', $option);
                        // Check if the option was selected
                        $selected = (isset($dataSaveInfo[$row['2']]) && $dataSaveInfo[$row['2']] == $value) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($name) . '</option>';
                    }
                    echo '</select>';
                } else {
                    $inputType = ($index <= 6) ? 'text' : 'number';
                    // If there are no options, create a number input
                    echo '<input class="form-control"  type="' . $inputType . '" id="' . $row['2'] . '" name="' . $row['2'] . '" value="' . (isset($dataSaveInfo[$row['2']]) ? htmlspecialchars($dataSaveInfo[$row['2']]) : '') . '" required>';
                }
                // render input file
                if ($row['3'] == '1') {
                    echo "<input class='form-control d-none' type='file' id='" . htmlspecialchars($row['2']) . "_img' name='" . htmlspecialchars($row['2']) . "_img[]' multiple onchange='showFileNames(this)'>";
                    echo "<button type='button' class='btn btn-secondary m-2 d-inline-block col-sm-6 col-lg-4 col-xl-4 col-xxl-4' onclick='document.getElementById(\"" . htmlspecialchars($row['2']) . "_img\").click()'><i class='ph ph-upload-simple'></i> Upload image</button>";
                }
                echo '</div>';
                echo "<div id='" . htmlspecialchars($row['2']) . "_img_names' class='row'></div>";
                echo '</div>';
                echo '</div>';
                $index++;
            }
            // Add a submit button
            echo '<div class="form-group mt-3 d-flex justify-content-end gap-2">
                    <button id="save-info" type="button" class="btn btn-light" onclick="saveInfo()">Save</button>
                    <button id="submit-info" type="button" class="btn btn-success" onclick="submitInfo()">Submit</button>
                </div>';
            echo '</form>';
        }
        ?>
    </div>

    <script>
        // JavaScript function to toggle the visibility of notes
        function toggleNote(variable) {
            const note = document.getElementById(variable + '-note');
            if (note.style.display === "none" || note.style.display === "") {
                note.style.display = "block"; // Show the note
            } else {
                note.style.display = "none"; // Hide the note
            }
        }
        // enable popover
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
        const popover = new bootstrap.Popover('.popover-dismiss', {
            trigger: 'focus'
        })

        // Toggle the responsive class to show/hide the menu
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('responsive');
        }

        // handle save and submit button
        function saveInfo() {
            var form = document.getElementById('form-info');
            const stationName = document.getElementById('station_name').value;
            var formData = new FormData(form);

            // Add the station name to the form data
            formData.append('station_name', stationName);

            fetch('backend/site/save_info.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Data saved successfully!');
                    } else {
                        alert('Failed to save data.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        };

        function submitInfo() {
            var form = document.getElementById('form-info');
            const stationName = document.getElementById('station_name').value;
            var formData = new FormData(form);

            // Add the station name to the form data
            formData.append('station_name', stationName);

            fetch('backend/site/submit_info.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Data submitted successfully!');
                    } else {
                        alert('Failed to submit data.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        };

        function showFileNames(input) {
            var fileNamesDiv = document.getElementById(input.id + '_names');
            fileNamesDiv.innerHTML = ''; // Clear previous file names
            for (var i = 0; i < input.files.length; i++) {
                fileNamesDiv.innerHTML += '<div class="col-md-12 text-end">' + input.files[i].name + '</div>';
            }
        }
    </script>
</body>

</html>
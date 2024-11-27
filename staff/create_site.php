<?php
session_start();

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id']; 
$emailJsonPath = "../database/site/{$userEmail}.json";

// Function to read the JSON file and decode it
function getInputElements() {
    $jsonFile = '../database/template/input_ele.json';
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        if ($jsonData === false) {
            echo "Error reading the JSON file.";
            return [];
        }
        $decodedData = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON decode error: " . json_last_error_msg();
            return [];
        }
        return $decodedData;
    } else {
        echo "File does not exist: $jsonFile";
        return [];
    }
}

// Handle the form submission
$showForm = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the station name and reference station from the POST data
    $stationName = $_POST['station_name'];
    $referenceStation = $_POST['reference_station'];
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

 .form-container {
    max-width: 600px;
    margin: 0 auto;
}

.form-group {
    display: flex; /* Align items on the same line */
    align-items: center; /* Vertically align items in the center */
    margin-bottom: 20px;
}

.form-group label {
    flex: 1; /* Allow the label to take available space */
    margin-right: 10px; /* Space between label and input */
}

.form-group .note-icon {
    margin-left: 5px;
    font-size: 1.2em;
    cursor: pointer;
}

.form-group input[type="text"], .form-group select {
    flex: 3; /* Allow the input field to take more space */
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
    display: none; /* Hide the note by default */
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
            <div class="form-group">
                <label for="station_name">Station Name:</label>
                <input type="text" id="station_name" name="station_name" value="<?php echo isset($_POST['station_name']) ? htmlspecialchars($_POST['station_name']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="reference_station">Reference Station:</label>
                <input type="text" id="reference_station" name="reference_station" value="<?php echo isset($_POST['reference_station']) ? htmlspecialchars($_POST['reference_station']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Create</button>
            </div>
        </form>

        <!-- Displaying form elements from input_ele.json only after the form is submitted -->
        <?php
        // Fetching the data from the JSON file
        $elements = getInputElements();

        // Only display the dynamic form fields if elements are found and the form has been submitted
        if (!empty($elements) && $showForm) {
            echo '<form method="POST" id="dynamicForm" class="dynamic-form show">';

            foreach ($elements as $element) {
                // Generate the field dynamically
                echo '<div class="form-group">';
                echo '<label for="' . $element['Variable'] . '">' . $element['Item'] . ':</label>';
                
                // Check if the note is not empty before displaying the icon
                if (!empty($element['Note'])) {
                    echo '<span class="note-icon" onclick="toggleNote(\'' . $element['Variable'] . '\')">ℹ️</span>';
                }

                // Add the note display if there's a note
                echo '<div class="note" id="' . $element['Variable'] . '-note">' . $element['Note'] . '</div>';
                
                // If there are options, create a select dropdown
                if (!empty($element['Option'])) {
                    $options = explode(' / ', $element['Option']);
                    echo '<select id="' . $element['Variable'] . '" name="' . $element['Variable'] . '">';
                    echo '<option value="" disabled selected>Choose</option>'; // Placeholder option
                    foreach ($options as $option) {
                        // Check if the option was selected
                        $selected = (isset($_POST[$element['Variable']]) && $_POST[$element['Variable']] == $option) ? 'selected' : '';
                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                    }
                    echo '</select>';
                } else {
                    // If there are no options, create a number input
                    echo '<input type="number" id="' . $element['Variable'] . '" name="' . $element['Variable'] . '" value="' . (isset($_POST[$element['Variable']]) ? htmlspecialchars($_POST[$element['Variable']]) : '') . '" required>';
                }
               
                echo '</div>';
            }

            echo '<div class="form-group">';
            echo '<button type="submit" class="submit-btn">Submit</button>';
            echo '</div>';
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
    </script>
    <script>
        // Toggle the responsive class to show/hide the menu
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('responsive');
        }
    </script>
</body>
</html>


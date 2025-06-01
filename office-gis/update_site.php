<?php
session_name("ele_5g_office_gis");
session_start();

// Set default language to 'en'
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Change language if selected
if (isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
}

$language = $_SESSION['language'];

// Check if the user is logged in; if not, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'office_gis') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

// get param from url
$stationName = isset($_GET['station_name']) ? $_GET['station_name'] : '';

include '../helper/getDataFormXlsx.php';
include '../helper/general.php';

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id'];
$emailJsonPath = "../database/site/{$userEmail}.json";

$infoCurrentUserRes = getUserInfo($userEmail);
if ($infoCurrentUserRes['status'] === 'success') {
    $infoCurrentUser = $infoCurrentUserRes['data'];
} else {
    echo "<script>alert('Failed to get user information! Please try again.'); window.location.href = 'index.php';</script>";
    exit();
}
$currentProvince = $infoCurrentUser['province'];

// get data form init_form.xlsx
$dataFormInit = getDataFormXlsx('../database/template/init_form.xlsx');

$usersData = getDataFromJson('../database/account/users.json');
echo '<script>';
echo 'const fullName = ' . json_encode($fullName) . ';';
echo 'const userEmail = ' . json_encode($userEmail) . ';';
echo 'const usersData = ' . json_encode($usersData['data']) . ';';
echo 'const currentProvince = ' . json_encode($currentProvince) . ';';
echo '</script>';

// handle hide fields
$selectShowField = 'number_of_dc_units';
$hideFields = [
    'dc1_unused_cb',
    'dc1_space_for_additional_cb',
    'dc1_additional_cb',
    'dc1_reason_for_not_installing_cb',
    'dc2_unused_cb',
    'dc2_space_for_additional_cb',
    'dc2_additional_cb',
    'dc2_reason_for_not_installing_cb',
    'dc3_unused_cb',
    'dc3_space_for_additional_cb',
    'dc3_additional_cb',
    'dc3_reason_for_not_installing_cb',
];


// Handle get information from station name
$showForm = true;

// split station name 'BAT00002' to 'BAT'
$stationNameFile = substr($stationName, 0, 3);
$filePathStation = '../database/template/station_type.xlsx';
// check filePathStation exists
if (!file_exists($filePathStation)) {
    echo "<script>alert('Station name not found! Please try again.'); window.location.href = 'create_site.php';</script>";
    exit();
}
$dataStationName = getDataFormXlsx($filePathStation);

$stationType = [];
foreach ($dataStationName as $key => $value) {
    $stationType[] = $value[0];
}
// set unique station type
$stationType = array_unique($stationType);
if (!empty($stationType) && count($stationType) > 1) {
    $stationTypeCell = [];
    $stationTypeCellString = '';
    $index = 1;
    foreach ($stationType as $key => $value) {
        $stationTypeCell[] = $index . ': ' . $value;
        $stationTypeCellString .= $index . ': ' . $value . "\n";
        $index++;
    }
}

// get saved data from stationName.json
$filePath = '../database/site/dataSubmit/' . $stationName . '.json';
$dataResponse = getDataFromJson($filePath);
if ($dataResponse['status'] === 'success') {
    $dataSaveInfo = $dataResponse['data'];
    // Do something with the data
} else {
    // $dataSaveInfo = $dataReferenceStation;
    $dataSaveInfo = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Head ELE GIS's Dashboard</title>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="header">
        <h1><?= translate('Survey Site', $language) ?></h1>
    </div>

    <div class="menu">
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="icon">
            <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
        </div>
        <a href="index.php"><?= translate('Home', $language) ?></a>
        <a href="all_site.php"><?= translate('Station Management', $language) ?></a>
        <a href="all_survey_station.php"><?= translate('Survey Station Management', $language) ?></a>
        <!-- add select languages -->
        <!-- add select languages -->
        <form method="POST" action="">
            <select class="form-select form-select-sm m-2" name="language" id="language" onchange="this.form.submit()" style="width: 150px;">
                <option value="en" <?php echo $_SESSION['language'] == 'en' ? 'selected' : ''; ?>><?= translate('English', $language) ?></option>
                <option value="ca" <?php echo $_SESSION['language'] == 'ca' ? 'selected' : ''; ?>><?= translate('Cambodian', $language) ?></option>
            </select>
        </form>
        <a href="logout.php" class="logout"><?= translate('Logout', $language) ?></a>
    </div>

    <div class="container">
        <div class="welcome-message">
            <p><?php echo translate('Welcome', $language); ?>, <?php echo $fullName; ?>!</p>
        </div>

        <!-- Form to submit station name and reference station -->
        <form id="form-create">
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="station_name"><?= translate('Station Name', $language) ?>:</label>
                </div>
                <div class="col-md-6">
                    <input type="text" id="station_name" name="station_name" value="<?php echo $stationName; ?>" required disabled>
                </div>
            </div>
        </form>
        <hr>
        <!-- Displaying form elements from input_ele.json only after the form is submitted -->
        <?php

        if (!empty($dataFormInit) && $showForm) {
            echo '<form id="form-info" enctype="multipart/form-data">';

            $index = 0;

            foreach ($dataFormInit as $row) {
                if ($index == 5) {
                    $row[4] = $stationTypeCellString;
                    $row[5] = $stationTypeCellString;
                    $row[6] = $stationTypeCellString;
                    $row[7] = $stationTypeCellString;
                }
                // check if field is hidden
                $classHidden = '';
                $requiredField = 'required';
                $requiredImgField = 'required';
                $imageList = [];
                if (in_array($row['2'], $hideFields)) {
                    $classHidden = 'd-none';
                    $requiredField = '';
                    $requiredImgField = '';
                }
                if (isset($dataSaveInfo[$row['2'] . '_link']) && !empty($dataSaveInfo[$row['2'] . '_link'])) {
                    $requiredImgField = '';
                    // convert string to array by ','
                    $imageList = explode(',', $dataSaveInfo[$row['2'] . '_link']);
                }
                // check if hidden field has value then remove hidden class
                if (isset($dataSaveInfo[$row['2']]) && !empty($dataSaveInfo[$row['2']])) {
                    $classHidden = '';
                }

                // // check reference station not same station name then set $imageList = [] and $requiredImgField = 'required'
                // if ($referenceStation !== '' && $referenceStation !== $stationName) {
                //     $imageList = [];
                //     $requiredImgField = 'required';
                // }

                // $classHidden = '';
                // Generate the field dynamically
                echo '<div id="' . $row['2'] . '_row" class="form-group row ' . $classHidden . '">';
                echo '<div class="col-md-6">
                        <label tabindex="0" role="button" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus" data-bs-content="' . ($language === 'en' ? $row['0'] : $row['1']) . '">' . ($language === 'en' ? $row['1'] : $row['0']) . ':</label>
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
                    
                    if ($index == 5) {
                        $options = $stationTypeCell;
                    }else {
                        $options = $language === 'en' ? explode("\n", $row['5']) : explode("\n", $row['4']);
                    }

                    if ($row['2'] == $selectShowField) {
                        echo '<select class="form-select" id="' . $row['2'] . '" name="' . $row['2'] . '" required onchange="showHideFields(this)">';
                    } else {
                        echo '<select class="form-select" id="' . $row['2'] . '" name="' . $row['2'] . '" ' . $requiredField . '>';
                    }
                    echo '<option value="" disabled selected>' . translate('Choose', $language) . '</option>'; // Placeholder option
                    foreach ($options as $option) {
                        // Split each option by the colon to get the value and name
                        list($value, $name) = explode(': ', $option);
                        // Check if the option was selected
                        $selected = (isset($dataSaveInfo[$row['2']]) && $dataSaveInfo[$row['2']] == $value) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
                    }
                    echo '</select>';
                } else {
                    $inputType = ($index <= 6) ? 'text' : 'number';
                    $valueInput = isset($dataSaveInfo[$row['2']]) ? htmlspecialchars($dataSaveInfo[$row['2']]) : (isset($dataStationNameArray[$row['2']]) ? $dataStationNameArray[$row['2']] : '');
                    $valueInput = isset($dataStationNameArray[$row['2']]) ? $dataStationNameArray[$row['2']] : (isset($dataSaveInfo[$row['2']]) ? htmlspecialchars($dataSaveInfo[$row['2']]) : '');

                    // If there are no options, create a number input
                    echo '<input class="form-control"  type="' . $inputType . '" id="' . $row['2'] . '" name="' . $row['2'] . '" value="' . $valueInput . '" ' . $requiredField . '>';
                }
                // render input file
                if ($row['3'] == '1') {
                    echo "<input class='form-control d-none' disabled type='file' id='" . htmlspecialchars($row['2']) . "_img' name='" . htmlspecialchars($row['2']) . "_img[]' multiple onchange='showFileNames(this)' " . $requiredImgField . ">";
                    echo "<button id='" . htmlspecialchars($row['2']) . "_img' type='button' class='btn btn-secondary m-2 d-inline-block col-sm-6 col-lg-4 col-xl-4 col-xxl-4' onclick='document.getElementById(\"" . htmlspecialchars($row['2']) . "_img\").click()'><i class='ph ph-upload-simple'></i> " . translate('Upload image', $language) . "</button>";
                }
                echo '</div>';
                echo "<div id='" . htmlspecialchars($row['2']) . "_img_names' class='row'>";
                foreach ($imageList as $image) {
                    $indexImg = 1;
                    echo "<a href=" . $image . " class='col-md-12 text-end' target='_blank'> Image " . $indexImg . "</a>";
                    $indexImg++;
                }
                echo "</div>";
                echo '</div>';
                echo '</div>';
                $index++;
            }

            // get approval status by user role
            $approvalStatus = getApprovalStatusByRole($dataSaveInfo['approval'], $_SESSION['role']);
            // check if approval status is pending then show button save and submit
            if ($approvalStatus['status'] === 'pending') {
                $hiddenBtn = '';
            } else {
                $hiddenBtn = 'd-none';
            }
            // Add a submit button
            echo '<div class="form-group mt-3 d-flex justify-content-end gap-2 ' . $hiddenBtn . '">
                    <button id="save-info" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">' . translate('Reject', $language) . '</button>
                    <button id="submit-info" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalApprove">' . translate('Approve', $language) . '</button>
                </div>';
            echo '</form>';
        }
        ?>

        <!-- Modal reject -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"><?= translate('Confirm reject', $language) ?></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label"><?= translate('Reason', $language) ?>:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Cancel', $language) ?></button>
                        <button type="button" class="btn btn-primary" id="rejectSubmitButton" onclick="handleReject()"><?= translate('Submit', $language) ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal approve -->
        <div class="modal fade" id="exampleModalApprove" tabindex="-1" aria-labelledby="exampleModalApproveLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalApproveLabel"><?= translate('Confirm Approve', $language) ?></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><?= translate('Are you sure you want to approve this station?', $language) ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Cancel', $language) ?></button>
                        <button type="button" class="btn btn-primary" id="rejectSubmitButton" onclick="handleApprove()"><?= translate('Submit', $language) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript function to validate the form before saving or submitting
        function validateForm(action) {
            var form = document.getElementById('form-info');
            var fileInputs = document.querySelectorAll('input[type="file"]');
            var allFilesValid = true;

            fileInputs.forEach(function(fileInput) {
                if (fileInput.required && fileInput.files.length === 0) {
                    allFilesValid = false;
                    fileInput.classList.add('is-invalid');
                } else {
                    fileInput.classList.remove('is-invalid');
                }
            });

            if (form.checkValidity() && allFilesValid) {
                if (action === 'save') {
                    saveInfo();
                } else if (action === 'submit') {
                    submitInfo();
                }
            } else {
                if (!allFilesValid) {
                    alert('Please upload the required files.');
                }
                form.reportValidity();
            }
        }
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

        // handle reject
        function handleReject() {
            var message = document.getElementById('message-text').value;
            fetch('backend/site/reject_info.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        station_name: document.getElementById('station_name').value,
                        email: userEmail,
                        message: message,
                        role: '<?= $_SESSION['role'] ?>'
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(async (data) => {
                    // add alert processing
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Sending message to Telegram...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    // send message to telegram
                    const staffInfo = getUserInfoByEmail(data.data.email);
                    let telegramMessage = '';

                    telegramMessage = `**The Request Rejected!**\n` +
                        `Creator: ${staffInfo.fullname} - ${staffInfo.email}\n` +
                        `Station Name: ${document.getElementById('station_name').value}\n` +
                        `Branch: ${data.data.branch}\n` +
                        `Station Code (7 digits): ${data.data.station_code_7_digits}\n` +
                        `Station Code: ${data.data.station_code}\n` +
                        `Station Type: ${data.data.station_type}\n` +
                        `Group: ${data.data.group}\n` +
                        `Reject by: <?php echo $fullName . ' - ' . $userEmail ?>\n` +
                        `Reason: ${message}\n` +
                        `Update At: ${new Date().toLocaleString()}\n`;


                    // Gửi tin nhắn đến Telegram
                    await fetch('../sendTelegram.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: telegramMessage,
                            id_telegram: staffInfo.idtele // Truyền thêm thông tin operator_phone
                        })
                    });
                    if (data.status === 'success') {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            text: "Rejected successfully!",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // alert('Data submitted successfully!');
                            // reload this page
                            location.reload();
                        });
                    } else {
                        Swal.close();
                        alert(data.message);
                    }
                })
                .catch(error => {
                    Swal.close();
                    alert(error.message);
                });
        };

        function handleApprove() {
            // Get all form data
            const form = document.getElementById('form-info');
            const formData = new FormData(form);
            
            // Convert FormData to object
            const formDataObject = {};
            formData.forEach((value, key) => {
                formDataObject[key] = value;
            });
            
            fetch('backend/site/approve_info.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        station_name: document.getElementById('station_name').value,
                        email: userEmail,
                        role: '<?= $_SESSION['role'] ?>',
                        site_data: formDataObject
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(async (data) => {
                    if (data.status === 'success') {
                        // add alert processing
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Sending message to Telegram...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        // send message to telegram
                        const staffInfo = getUserInfoByEmail(data.data.email);
                        let telegramMessage = '';

                        telegramMessage = `**The Request Approved!**\n` +
                            `Creator: ${staffInfo.fullname} - ${staffInfo.email}\n` +
                            `Station Name: ${document.getElementById('station_name').value}\n` +
                            `Branch: ${data.data.branch}\n` +
                            `Station Code (7 digits): ${data.data.station_code_7_digits}\n` +
                            `Station Code: ${data.data.station_code}\n` +
                            `Station Type: ${data.data.station_type}\n` +
                            `Group: ${data.data.group}\n` +
                            `Approve by: <?php echo $fullName . ' - ' . $userEmail ?>\n` +
                            `Update At: ${new Date().toLocaleString()}\n`;


                        // Gửi tin nhắn đến Telegram
                        await fetch('../sendTelegram.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                message: telegramMessage,
                                id_telegram: staffInfo.idtele // Truyền thêm thông tin operator_phone
                            })
                        });

                        // Tạo nội dung tin nhắn để gửi cho các user có role là 'bod_pro_gis'
                        usersData.forEach(async function(user) {
                            if (user.role === 'office_vtc' && user.province === currentProvince) {
                                let telegramMessage = '';

                                telegramMessage = `**New request needs approval!**\n` +
                                    `Creator: ${staffInfo.fullname} - ${staffInfo.email}\n` +
                                    `Station Name: ${document.getElementById('station_name').value}\n` +
                                    `Branch: ${data.data.branch}\n` +
                                    `Station Code (7 digits): ${data.data.station_code_7_digits}\n` +
                                    `Station Code: ${data.data.station_code}\n` +
                                    `Station Type: ${data.data.station_type}\n` +
                                    `Group: ${data.data.group}\n` +
                                    `Approve by Head ELE GIS: <?php echo $fullName . ' - ' . $userEmail ?>\n` +
                                    `Update At: ${new Date().toLocaleString()}\n`;

                                // Gửi tin nhắn đến Telegram
                                await fetch('../sendTelegram.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        message: telegramMessage,
                                        id_telegram: user.idtele
                                    })
                                });


                            }
                        });
                        // add alert success
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            text: "Approved successfully!",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // alert('Data submitted successfully!');
                            // reload this page
                            location.reload();
                        });
                    } else {
                        Swal.close();
                        alert(data.message);
                    }
                })
                .catch(error => {
                    Swal.close();
                    alert(error.message);
                });
        };

        function showFileNames(input) {
            var fileNamesDiv = document.getElementById(input.id + '_names');
            fileNamesDiv.innerHTML = ''; // Clear previous file names
            for (var i = 0; i < input.files.length; i++) {
                fileNamesDiv.innerHTML += '<div class="col-md-12 text-end">' + input.files[i].name + '</div>';
            }
        }

        // function get information from json file of user by email
        function getUserInfoByEmail(email) {
            var userInfo = usersData.find(user => user.email === email);
            return userInfo;
        }


        // show hide fields
        function showHideFields(select) {
            var hideFields = [
                'dc1_unused_cb',
                'dc1_space_for_additional_cb',
                'dc1_additional_cb',
                'dc1_reason_for_not_installing_cb',
                'dc2_unused_cb',
                'dc2_space_for_additional_cb',
                'dc2_additional_cb',
                'dc2_reason_for_not_installing_cb',
                'dc3_unused_cb',
                'dc3_space_for_additional_cb',
                'dc3_additional_cb',
                'dc3_reason_for_not_installing_cb',
            ];
            const numberRowShow = select.value;
            for (var i = 1; i <= numberRowShow; i++) {
                // get fields with start dc1
                const fields = hideFields.filter(field => field.startsWith('dc' + i));
                fields.forEach(field => {
                    document.getElementById(field + '_row').classList.remove('d-none');
                    // reset value on showFields
                    // document.getElementById(field).value = '';
                    // add attribute required
                    document.getElementById(field).setAttribute('required', 'required');
                    // remove field on hideFields
                    hideFields = hideFields.filter(item => item !== field);
                });
            }
            // hide other fields
            hideFields.forEach(field => {
                document.getElementById(field + '_row').classList.add('d-none');
                // reset value on hideFields
                document.getElementById(field).value = '';
                // remove attribute required
                document.getElementById(field).removeAttribute('required');
            });
        }
    </script>
</body>

</html>
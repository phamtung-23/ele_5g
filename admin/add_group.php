<?php
session_name("ele_5g_admin");
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
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('You are not logged in! Please log in again.'); window.location.href = 'index.php';</script>";
    exit();
}

// get param from url
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : '';

include '../helper/getDataFormXlsx.php';
include '../helper/general.php';

$fullName = $_SESSION['full_name'];
$userEmail = $_SESSION['user_id'];

// filter user by userId
$usersDataRes = getUserInfoById($userId, '../database/account/users.json');

if ($usersDataRes['status'] === 'fail') {
    echo "<script>alert('User not found!'); window.location.href = 'index.php';</script>";
    exit();
}

$usersData = $usersDataRes['data'];

$provincePath = '../database/template/' . $usersData['province'] . '.xlsx';
// check if the file exists
if (!file_exists($provincePath)) {
    echo "<script>alert('The province file does not exist!'); window.location.href = 'index.php';</script>";
    exit();
}
// get data form [province].xlsx
$dataProvince = getDataFormXlsx($provincePath);
// check dataProvince is empty
$listGroup = [];
if (!empty($dataProvince)) {
    foreach ($dataProvince as $row) {
        // get group in row[3] and save in listGroup
        $listGroup[] = $row[3];
        // remove duplicate value in listGroup
        $listGroup = array_unique($listGroup);
    }
}

$listProvincePath = '../database/template/province.xlsx';
// check if the file exists
if (!file_exists($listProvincePath)) {
    echo "<script>alert('The province file does not exist!');</script>";
    exit();
}

// get data form province.xlsx
$dataListProvince = getDataFormXlsx($listProvincePath);


echo '<script>';
echo 'const currentUser = ' . json_encode($usersData) . ';';
echo '</script>';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin's Dashboard</title>
    <!-- add file tyles.css -->
    <link rel="stylesheet" href="styles.css">
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
        <h1><?= translate("Admin's Dashboard", $language) ?></h1>
    </div>

    <div class="menu">
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="icon">
            <img src="../images/icon.jpg" alt="Home Icon" class="menu-icon">
        </div>
        <a href="index.php"><?= translate('Home', $language) ?></a>
        <a href="all_site.php"><?= translate('Station Management', $language) ?></a>
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
            <p><?php echo translate('User Information', $language); ?>, <?= $usersData['fullname'] ?>!</p>
        </div>
        <hr>

        <!-- Form to submit station name and reference station -->
        <form id="form-create">
            <div class="form-group row mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="email"><?= translate('Email', $language) ?>:</label>
                </div>
                <div class="col-md-8">
                    <input class="form-control" type="email" id="email" name="email" value="<?= $usersData['email']; ?>" required disabled>
                </div>
            </div>
            <div class="form-group row mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="role"><?= translate('Role', $language) ?>:</label>
                </div>
                <div class="col-md-8">
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled selected><?= translate('Select Role', $language) ?></option>
                        <option value="staff" <?= $usersData['role'] === 'staff' ? 'selected' : ''; ?>><?= translate('Staff', $language) ?></option>
                        <option value="bod_pro_gis" <?= $usersData['role'] === 'bod_pro_gis' ? 'selected' : ''; ?>><?= translate('BoD GIS Province', $language) ?></option>
                        <option value="office_gis" <?= $usersData['role'] === 'office_gis' ? 'selected' : ''; ?>><?= translate('Head ELE GIS', $language) ?></option>
                        <option value="office_vtc" <?= $usersData['role'] === 'office_vtc' ? 'selected' : ''; ?>><?= translate('Head ELE VTC', $language) ?></option>
                    </select>
                    <!-- <input class="form-control" type="role" id="role" name="role" value="<?= $usersData['role']; ?>" required disabled> -->
                </div>
            </div>
            <div class="form-group row mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="province"><?= translate('Province', $language) ?>:</label>
                </div>
                <div class="col-md-8">
                <select class="form-select" id="province" name="province" required>
                        <option value="" disabled selected><?= translate('Select Province', $language) ?></option>
                        <?php
                        foreach ($dataListProvince as $row) {
                            if ($row[0] === $usersData['province']) {
                                echo '<option value="' . $row[0] . '" selected>' . $row[0] . '</option>';
                            } else {
                                echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
                            }
                        }
                        ?>
                </select>
                </div>
            </div>
            <div class="form-group row mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="phone"><?= translate('Phone', $language) ?>:</label>
                </div>
                <div class="col-md-8">
                    <input class="form-control" type="text" id="phone" name="phone" value="<?= $usersData['phone']; ?>" required disabled>
                </div>
            </div>
            <div class="form-group row mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="idtele"><?= translate('ID telegram', $language) ?>:</label>
                </div>
                <div class="col-md-8">
                    <input class="form-control" type="text" id="idtele" name="idtele" value="<?= $usersData['idtele']; ?>" required disabled>
                </div>
            </div>
            <?php
            if ($usersData['role'] === 'staff') {
            ?>
                <div class="form-group row mt-3">
                    <div class="col-md-4">
                        <label class="form-label" for="group"><?= translate('Group', $language) ?>:</label>
                    </div>
                    <!-- add select with option is listGroup -->
                    <div class="col-md-8">
                        <select class="form-select" id="group" name="group" required>
                            <option value="" disabled selected><?= translate('Select Group', $language) ?></option>
                            <?php
                            foreach ($listGroup as $group) {
                                if (isset($usersData['group']) && $group === $usersData['group']) {
                                    echo '<option value="' . $group . '" selected>' . $group . '</option>';
                                } else {
                                    echo '<option value="' . $group . '">' . $group . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mt-3 d-flex justify-content-end gap-2">
                    <?php
                    if (isset($usersData['group']) && $usersData['group'] !== '') {
                        echo '<button id="submit-info" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalApprove">' . translate('Update', $language) . '</button>';
                    } else {
                        echo '<button id="submit-info" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalApprove">' . translate('Add Group', $language) . '</button>';
                    }
                    ?>

                </div>
            <?php
            } else {
                echo '<div class="form-group mt-3 d-flex justify-content-end gap-2">';
                echo '<button id="submit-info" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalUpdate">' . translate('Update', $language) . '</button>';
                echo '</div>';
            }
            ?>
        </form>

        <!-- Modal confirm -->
        <div class="modal fade" id="exampleModalApprove" tabindex="-1" aria-labelledby="exampleModalApproveLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalApproveLabel">Confirm Add Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to add group with email <?= $usersData['email']; ?>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="rejectSubmitButton" onclick="handleAddGroup()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal confirm -->
        <div class="modal fade" id="exampleModalUpdate" tabindex="-1" aria-labelledby="exampleModalUpdateLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalUpdateLabel">Confirm Add Group</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to update information for email <?= $usersData['email']; ?>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="rejectSubmitButton" onclick="handleUpdateInfo()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const exampleModalUpdate = document.getElementById('exampleModalUpdate')
        const exampleModalApprove = document.getElementById('exampleModalApprove')
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

        function handleAddGroup() {
            const group = document.getElementById('group').value;
            const role = document.getElementById('role').value;
            const province = document.getElementById('province').value;
            const modal = bootstrap.Modal.getInstance(exampleModalApprove);
        
            if (role === '') {
                modal.hide();
                alert('Please select a role!');
                return;
            }
            if (province === '') {
                modal.hide();
                alert('Please select a province!');
                return;
            }
            if (group === '') {
                modal.hide();
                alert('Please select a group!');
                return;
            }
            fetch('backend/site/handle_add_group.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        group: group,
                        role,
                        province,
                        userId: currentUser.id
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
                        // // send message to telegram
                        // const staffInfo = getUserInfoByEmail(data.data.email);
                        // let telegramMessage = '';

                        // telegramMessage = `**The Request Approved!**\n` +
                        //     `Creator: ${staffInfo.fullname} - ${staffInfo.email}\n` +
                        //     `Station Name: ${document.getElementById('station_name').value}\n` +
                        //     `Branch: ${data.data.branch}\n` +
                        //     `Station Code (7 digits): ${data.data.station_code_7_digits}\n` +
                        //     `Station Code: ${data.data.station_code}\n` +
                        //     `Station Type: ${data.data.station_type}\n` +
                        //     `Group: ${data.data.group}\n` +
                        //     `Approve by: <?php echo $fullName . ' - ' . $userEmail ?>\n` +
                        //     `Update At: ${new Date().toLocaleString()}\n`;


                        // // Gửi tin nhắn đến Telegram
                        // await fetch('../sendTelegram.php', {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json'
                        //     },
                        //     body: JSON.stringify({
                        //         message: telegramMessage,
                        //         id_telegram: staffInfo.idtele // Truyền thêm thông tin operator_phone
                        //     })
                        // });

                        // add alert success
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            text: "Update successfully!",
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

        function handleUpdateInfo() {
            const role = document.getElementById('role').value;
            const province = document.getElementById('province').value;
            const modal = bootstrap.Modal.getInstance(exampleModalApprove);
        
            if (role === '') {
                modal.hide();
                alert('Please select a role!');
                return;
            }
            if (province === '') {
                modal.hide();
                alert('Please select a province!');
                return;
            }
            fetch('backend/site/handle_update_info.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        role,
                        province,
                        userId: currentUser.id
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
                        // // send message to telegram
                        // const staffInfo = getUserInfoByEmail(data.data.email);
                        // let telegramMessage = '';

                        // telegramMessage = `**The Request Approved!**\n` +
                        //     `Creator: ${staffInfo.fullname} - ${staffInfo.email}\n` +
                        //     `Station Name: ${document.getElementById('station_name').value}\n` +
                        //     `Branch: ${data.data.branch}\n` +
                        //     `Station Code (7 digits): ${data.data.station_code_7_digits}\n` +
                        //     `Station Code: ${data.data.station_code}\n` +
                        //     `Station Type: ${data.data.station_type}\n` +
                        //     `Group: ${data.data.group}\n` +
                        //     `Approve by: <?php echo $fullName . ' - ' . $userEmail ?>\n` +
                        //     `Update At: ${new Date().toLocaleString()}\n`;


                        // // Gửi tin nhắn đến Telegram
                        // await fetch('../sendTelegram.php', {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json'
                        //     },
                        //     body: JSON.stringify({
                        //         message: telegramMessage,
                        //         id_telegram: staffInfo.idtele // Truyền thêm thông tin operator_phone
                        //     })
                        // });

                        // add alert success
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            text: "Update successfully!",
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

        // function get information from json file of user by email
        function getUserInfoByEmail(email) {
            var userInfo = usersData.find(user => user.email === email);
            return userInfo;
        }
    </script>
</body>

</html>
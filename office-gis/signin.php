<?php
session_name("ele_5g_office_gis");
session_start();

// Set the session lifetime to 1 hour (3600 seconds)
ini_set('session.gc_maxlifetime', 3600); 
session_set_cookie_params(3600); // Ensure the session cookie also expires after 1 hour

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['login_email'];
    $password = $_POST['your_pass'];

    // Read the users.json file
    $file = '../database/account/users.json';
    if (file_exists($file)) {
        $users = json_decode(file_get_contents($file), true);
    } else {
       echo "<script>
                alert('User list not found!');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Check the account
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            // Verify the hashed password
            if (password_verify($password, $user['password'])) { // Compare the input password with the hashed password
                // Save user information in the session
                $_SESSION['user_id'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['fullname']; // Assuming 'full_name' exists in the user data

                // Record the session start time
                $_SESSION['login_time'] = time();

                // Redirect based on the user role
                if ($user['role'] == 'office_gis') {
                    header("Location: home.php");
                }
                exit();
            } else {
                echo "<script>
                        alert('Incorrect password!');
                        window.location.href = 'index.php';
                      </script>";
                exit();
            }
        }
    }

    echo "<script>
            alert('Account not found!');
            window.location.href = 'index.php';
          </script>";
}
?>

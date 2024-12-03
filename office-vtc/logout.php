<?php
session_name("ele_5g_office_vtc");
session_start();

// Clear all sessions
session_unset();
session_destroy();

// Redirect to the login page
echo "<script>alert('You have successfully logged out.'); window.location.href = 'index.php';</script>";
exit();
?>

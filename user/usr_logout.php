<?php
session_start();

// Clear session data
session_unset();
session_destroy();

// Clear cookies
setcookie('user_id', '', time() - 3600, "/");
setcookie('email', '', time() - 3600, "/");

// Redirect to the login page
header("Location: usr_login.php");
exit;
?>

<?php
session_start();

// Clear session data
session_unset();
session_destroy();

// Clear cookies by setting their expiration time to a past time
// setcookie('seller_id', '', time() - 3600, "/");
// setcookie('business_email', '', time() - 3600, "/");
// setcookie('store_name', '', time() - 3600, "/");

// Redirect to the login page
header("Location: usr_login.php");
exit;
?>

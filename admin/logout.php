<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// If the remember me cookie exists, remove it
if (isset($_COOKIE['remember_admin'])) {
    setcookie('remember_admin', '', time() - 3600, "/");
}

// Redirect to login page
header("Location: index.php");
exit();
?>

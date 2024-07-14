<?php
// Check if a session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../database/conn.php';
?>

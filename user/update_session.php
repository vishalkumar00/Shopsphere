<?php
session_start();
include '../database/conn.php';

if (isset($_POST['total_amount'])) {
    $_SESSION['cart_total_amount'] = $_POST['total_amount'];
}
?>

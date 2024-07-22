<?php
session_start();
include 'config.php';

$cartCount = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch cart count
    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cartCount = $row['count'] ?? 0;
}

echo $cartCount;
?>

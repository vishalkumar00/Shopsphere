<?php
session_start();
include '../database/conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    header("Location: usr_login.php");
    exit;
}

// Validate and sanitize inputs
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$variant_id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$user_id = $_SESSION['user_id'];

if ($product_id > 0 && $variant_id > 0 && $quantity > 0) {
    // Check if the item already exists in the cart
    $sql_check = "SELECT quantity FROM cart WHERE user_id = ? AND variant_id = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("ii", $user_id, $variant_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            // Item exists, update the quantity
            $stmt_check->bind_result($existing_quantity);
            $stmt_check->fetch();
            $new_quantity = $existing_quantity + $quantity;
            $sql_update = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND variant_id = ?";
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("iii", $new_quantity, $user_id, $variant_id);
                if ($stmt_update->execute()) {
                    $_SESSION['success_message'] = "Product quantity updated in cart.";
                } else {
                    $_SESSION['error_message'] = "Error: " . $conn->error;
                }
                $stmt_update->close();
            }
        } else {
            // Item does not exist, insert a new record
            $sql_insert = "INSERT INTO cart (user_id, product_id, variant_id, quantity, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("iiii", $user_id, $product_id, $variant_id, $quantity);
                if ($stmt_insert->execute()) {
                    $_SESSION['success_message'] = "Product added to cart successfully.";
                } else {
                    $_SESSION['error_message'] = "Error: " . $conn->error;
                }
                $stmt_insert->close();
            }
        }
        $stmt_check->close();
    } else {
        $_SESSION['error_message'] = "Error: " . $conn->error;
    }
} else {
    $_SESSION['error_message'] = "Invalid input data.";
}

// Redirect to the cart page after adding the product
header("Location: cart.php");
exit;
?>

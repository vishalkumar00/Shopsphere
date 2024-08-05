<?php
session_start();
include '../database/conn.php';

// Initialize response array
$response = ['success' => false, 'message' => '', 'cartItemCount' => 0, 'modal' => false];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    $response['message'] = 'You must be logged in to add items to the cart.';
    $response['modal'] = true; // Indicate to show modal
    echo json_encode($response);
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
                    $response['success'] = true;
                    $response['message'] = "Product quantity updated in cart.";
                } else {
                    $response['message'] = "Error: " . $conn->error;
                }
                $stmt_update->close();
            }
        } else {
            // Item does not exist, insert a new record
            $sql_insert = "INSERT INTO cart (user_id, product_id, variant_id, quantity, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("iiii", $user_id, $product_id, $variant_id, $quantity);
                if ($stmt_insert->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Product added to cart successfully.";
                } else {
                    $response['message'] = "Error: " . $conn->error;
                }
                $stmt_insert->close();
            }
        }
        $stmt_check->close();
    } else {
        $response['message'] = "Error: " . $conn->error;
    }
} else {
    $response['message'] = "Invalid input data.";
}

// Fetch the updated cart item count
$sql_count = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
if ($stmt_count = $conn->prepare($sql_count)) {
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $stmt_count->bind_result($total_items);
    $stmt_count->fetch();
    $response['cartItemCount'] = $total_items;
    $stmt_count->close();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>

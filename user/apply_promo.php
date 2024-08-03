<?php
session_start();
include '../database/conn.php';

$response = array('success' => false, 'message' => 'Invalid promo code.', 'discount_amount' => 0, 'total_amount' => 0);

// Retrieve and validate promo code
$promo_code = isset($_POST['promo_code']) ? $_POST['promo_code'] : null;

if ($promo_code) {
    $sql_promo = "SELECT discount_percent FROM promo_code WHERE promo_name = ? AND status = 'Active'";
    $stmt_promo = $conn->prepare($sql_promo);
    $stmt_promo->bind_param('s', $promo_code);
    $stmt_promo->execute();
    $result_promo = $stmt_promo->get_result();
    
    if ($result_promo->num_rows > 0) {
        $promo = $result_promo->fetch_assoc();
        $discount_percent = $promo['discount_percent'];
        
        // Retrieve subtotal from session
        $subtotal = isset($_SESSION['cart_total_price']) ? $_SESSION['cart_total_price'] : 0;
        $taxes = $subtotal * 0.13;
        $discount_amount = $subtotal * ($discount_percent / 100);
        $total_amount = ($subtotal + $taxes) - $discount_amount;
        
        $response['success'] = true;
        $response['message'] = 'Promo code applied successfully!';
        $response['discount_amount'] = $discount_amount;
        $response['total_amount'] = $total_amount;
    }
    $stmt_promo->close();
}

echo json_encode($response);
?>

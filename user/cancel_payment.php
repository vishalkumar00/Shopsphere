<?php
include '../database/conn.php';

// Fetch the order ID from the request
$orderID = $_GET['order_id'] ?? null;

if (!$orderID) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

// Delete from order_items where the order is not in transactions
$sql_delete_order_items = "
    DELETE FROM order_items 
    WHERE order_id = ? 
    AND NOT EXISTS (SELECT 1 FROM transactions WHERE transactions.order_id = order_items.order_id)";
$stmt_delete_order_items = $conn->prepare($sql_delete_order_items);
$stmt_delete_order_items->bind_param('i', $orderID);
$stmt_delete_order_items->execute();
$stmt_delete_order_items->close();

// Delete from orders where the order is not in transactions
$sql_delete_orders = "
    DELETE FROM orders 
    WHERE order_id = ? 
    AND NOT EXISTS (SELECT 1 FROM transactions WHERE transactions.order_id = orders.order_id)";
$stmt_delete_orders = $conn->prepare($sql_delete_orders);
$stmt_delete_orders->bind_param('i', $orderID);
$stmt_delete_orders->execute();
$stmt_delete_orders->close();

// Redirect to cancellation confirmation page or return a JSON response
header("Location: checkout.php");  
exit();
?>

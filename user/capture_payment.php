<?php
require '../vendor/autoload.php';
require 'paypal_config.php'; 
include '../database/conn.php';

use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

// Fetch the PayPal order ID from the query parameters
$paypalOrderID = $_GET['token'] ?? null;

if (!$paypalOrderID) {
    echo json_encode(['error' => 'PayPal order ID is required']);
    exit;
}

// Set up PayPal request to capture payment
$request = new OrdersCaptureRequest($paypalOrderID);
$request->prefer('return=representation');

try {
    $response = $client->execute($request);
    $status = $response->result->status;

    if ($status === 'COMPLETED') {
        // Payment was successful
        
        // Insert a record into the transactions table
        $orderID = $_GET['order_id'];
        $amount = $response->result->purchase_units[0]->amount->value;
        $sql_transaction = "INSERT INTO transactions (order_id, amount, status, paypal_transaction_id) VALUES (?, ?, 'Completed', ?)";
        $stmt_transaction = $conn->prepare($sql_transaction);
        $stmt_transaction->bind_param('ids', $orderID, $amount, $paypalOrderID);
        $stmt_transaction->execute();
        $stmt_transaction->close();
        
        // Delete cart items for the current user
        session_start(); 
        $user_id = $_SESSION['user_id'];  
        $sql_delete_cart = "DELETE FROM cart WHERE user_id = ?";
        $stmt_delete_cart = $conn->prepare($sql_delete_cart);
        $stmt_delete_cart->bind_param('i', $user_id);
        $stmt_delete_cart->execute();
        $stmt_delete_cart->close();
        
        // Redirect to success page
        header("Location: success.php?order_id=$orderID");
        exit();
    } else {
        // Payment was not successful
        $error_message = 'Payment was not successful.';
        
        // Insert a record into the transactions table with error details
        $sql_transaction = "INSERT INTO transactions (order_id, amount, status, paypal_transaction_id, error_message) VALUES (?, ?, 'Failed', ?, ?)";
        $stmt_transaction = $conn->prepare($sql_transaction);
        $stmt_transaction->bind_param('idss', $orderID, $amount, $paypalOrderID, $error_message);
        $stmt_transaction->execute();
        $stmt_transaction->close();
        
        // Redirect to error page
        header("Location: error.php");
        exit();
    }
} catch (HttpException $ex) {
    // Handle exceptions
    $error_message = $ex->getMessage();
    
    // Insert a record into the transactions table with error details
    $sql_transaction = "INSERT INTO transactions (order_id, amount, status, paypal_transaction_id, error_message) VALUES (?, ?, 'Failed', ?, ?)";
    $stmt_transaction = $conn->prepare($sql_transaction);
    $stmt_transaction->bind_param('idss', $orderID, $amount, $paypalOrderID, $error_message);
    $stmt_transaction->execute();
    $stmt_transaction->close();
    
    // Redirect to error page
    header("Location: error.php");
    exit();
}
?>

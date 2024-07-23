<?php
require '../vendor/autoload.php';
require 'paypal_config.php'; 
include '../database/conn.php';

use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

// Fetch the order ID from the request (GET or POST depending on the form of request)
$orderID = $_GET['order_id'] ?? null;

if (!$orderID) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

// Fetch the total amount for the order
$sql = 'SELECT total_amount FROM orders WHERE order_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $orderID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $amount = $row['total_amount'];
} else {
    // Handle the error if the order is not found
    echo json_encode(['error' => 'Order not found']);
    exit;
}

// Set up PayPal request
$request = new OrdersCreateRequest();
$request->prefer('return=representation');
$request->body = [
    'intent' => 'CAPTURE',
    'purchase_units' => [
        [
            'amount' => [
                'currency_code' => 'CAD',
                'value' => number_format($amount, 2)
            ]
        ]
    ],
    'application_context' => [
        'return_url' => 'http://localhost/shopsphere/user/capture_payment.php?order_id=' . $orderID,
        'cancel_url' => 'http://localhost/shopsphere/user/cancel_payment.php?order_id=' . $orderID
    ]
];

try {
    $response = $client->execute($request);
    $paypalOrderID = $response->result->id;
    $approvalLink = $response->result->links[1]->href;

    // Redirect the user to PayPal for approval
    header("Location: $approvalLink");
    exit;
} catch (HttpException $ex) {
    echo json_encode(['error' => $ex->getMessage()]);
}
?>

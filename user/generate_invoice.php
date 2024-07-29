<?php
require('../assets/vendor/fpdf186/fpdf.php');
include '../database/conn.php'; 

// Check if order_id is set
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die('Invalid order ID.');
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$sql = "SELECT 
            o.order_id, 
            o.created_at, 
            o.total_amount, 
            o.shipping_status,
            oi.quantity, 
            pv.price, 
            pv.product_image, 
            p.product_name, 
            co.color_name AS color, 
            si.size_name AS size
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id
        LEFT JOIN products p ON pv.product_id = p.product_id
        LEFT JOIN colors co ON pv.color_id = co.color_id
        LEFT JOIN sizes si ON pv.size_id = si.size_id
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error preparing the statement: ' . $conn->error);
}

$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order = $result->fetch_assoc();
$order_items = [];
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}

$stmt->close();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Title
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
$pdf->Ln(10);

// Order Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Order ID: ' . $order['order_id'], 0, 1);
$pdf->Cell(0, 10, 'Order Date: ' . date('d M Y', strtotime($order['created_at'])), 0, 1);
$pdf->Cell(0, 10, 'Total Amount: $' . number_format($order['total_amount'], 2), 0, 1);
$pdf->Cell(0, 10, 'Shipping Status: ' . ucfirst($order['shipping_status']), 0, 1);
$pdf->Ln(10);

// Order Items
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Items:', 0, 1);
$pdf->SetFont('Arial', '', 12);

foreach ($order_items as $item) {
    $pdf->Cell(0, 10, $item['product_name'] . ' (' . $item['color'] . ', ' . $item['size'] . ')', 0, 1);
    $pdf->Cell(0, 10, 'Price: $' . number_format($item['price'], 2), 0, 1);
    $pdf->Cell(0, 10, 'Quantity: ' . $item['quantity'], 0, 1);
    $pdf->Cell(0, 10, 'Total: $' . number_format($item['quantity'] * $item['price'], 2), 0, 1);
    $pdf->Ln(5);
}

// Output PDF
$pdf->Output('I', 'Invoice_' . $order['order_id'] . '.pdf');
?>

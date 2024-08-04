<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require('../assets/vendor/fpdf186/fpdf.php');
include '../database/conn.php';
$user_id = $_SESSION['user_id'];

// Check if order_id is set
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die('Invalid order ID.');
}


$order_id = intval($_GET['order_id']);

// Fetch order details
$sql = "SELECT 
            o.order_id, 
            o.user_id, 
            o.total_amount, 
            o.shipping_status,
            o.created_at, 
            o.updated_at,
            o.shipping_address,
            o.city,
            o.province,
            o.postal_code,
            o.phone_number
        FROM orders o
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error preparing the statement: ' . $conn->error);
}

$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order = $result->fetch_assoc();

$stmt->close();

// Fetch user details
$sql_user = "SELECT 
                first_name, 
                last_name, 
                mobile_number, 
                email, 
                created_at,
                updated_at
             FROM users
             WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);

if ($stmt_user === false) {
    die('Error preparing the statement: ' . $conn->error);
}

$stmt_user->bind_param('i', $order['user_id']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

$user = $result_user->fetch_assoc();

$stmt_user->close();


// Fetch user orders and product details
$sql_orders = "SELECT 
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
             WHERE o.order_id = ?
             ORDER BY o.created_at DESC";
$stmt_orders = $conn->prepare($sql_orders);

if ($stmt_orders === false) {
    die('Error preparing the statement: ' . $conn->error);
}

$stmt_orders->bind_param('i', $order_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

$order_items = [];
while ($row = $result_orders->fetch_assoc()) {
    $order_items[] = $row;
}

$stmt_orders->close();



// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$x1 = 10; // Starting X coordinate
$y1 = 20; // Starting Y coordinate
$x2 = 200; // Ending X coordinate (page width - right margin)
$y2 = 20; // Ending Y coordinate (same as starting Y to make it horizontal)
// Title
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
$pdf->Ln(4);


// Draw the horizontal line
$pdf->Line($x1, $y1, $x2, $y2);
$x1 = 10; // Starting X coordinate
$y1 = 35; // Starting Y coordinate
$x2 = 200; // Ending X coordinate (page width - right margin)
$y2 = 35;

$pdf->Cell(0, 10, 'User Detail', 0, 1,);

// Draw the horizontal line
$pdf->Line($x1, $y1, $x2, $y2);
// Order user detail
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'User ID: ' . $order['user_id'], 0, 1);
$pdf->Cell(0, 10, 'Name: ' . $user['first_name'] . ' ' . $user['last_name'], 0, 1);

$pdf->Cell(0, 10, 'Mobile Number: ' . $user['mobile_number'], 0, 1);
$pdf->Cell(0, 10, 'Email: ' . $user['email'], 0, 1);
$x1 = 10; // Starting X coordinate
$y1 = 75; // Starting Y coordinate
$x2 = 200; // Ending X coordinate (page width - right margin)
$y2 = 75; // Ending Y coordinate (same as starting Y to make it horizontal)
$pdf->Line($x1, $y1, $x2, $y2);
// Order Details
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 10, 'Order Detail', 0, 0, 'L');

// Move to the right for Shipping Detail
$pdf->SetX(-50);
$pdf->Cell(0, 10, 'Shipping Detail', 0, 1, 'R');
$x1 = 10; // Starting X coordinate
$y1 = 83; // Starting Y coordinate
$x2 = 200; // Ending X coordinate (page width - right margin)
$y2 = 83; // Ending Y coordinate (same as starting Y to make it horizontal)
$pdf->Line($x1, $y1, $x2, $y2);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Order ID: ' . $order['order_id'], 0, 0,);
$pdf->SetX(-50);
$pdf->Cell(0, 10, 'Shipping Address: ' . $order['shipping_address'], 0, 1, 'R');
$pdf->Cell(0, 10, 'City: ' . $order['city'], 0, 1, 'R');
$pdf->Cell(0, 10, 'Province: ' . $order['province'], 0, 1, 'R');
$pdf->Cell(0, 10, 'Postal Code: ' . $order['postal_code'], 0, 1, 'R');
$pdf->Cell(0, 10, 'Phone Number: ' . $order['phone_number'], 0, 1, 'R');

//item infromation

$x1 = 10; // Starting X coordinate
$y1 = 145; // Starting Y coordinate
$x2 = 200; // Ending X coordinate (page width - right margin)
$y2 = 145; // Ending Y coordinate (same as starting Y to make it horizontal)
$pdf->Line($x1, $y1, $x2, $y2);
// Order Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Items:', 0, 1);
$pdf->SetFont('Arial', '', 12);

foreach ($order_items as $item) {
    // Get the correct path for the image
    // Construct the correct path for the image
    $image_path = '../uploads/' . $item['product_image'];
    // Define the width of the image and the gap between the image and text
    $image_width = 30;
    $text_margin = 40; // Margin between image and text

    // Save the current Y position
    $current_y = $pdf->GetY();

    // Check if the image file exists
    if (file_exists($image_path)) {
        // Print product image
        $pdf->Image($image_path, $pdf->GetX(), $current_y, $image_width);
    } else {
        // Print placeholder or error message if the image is not found
        $pdf->SetX($pdf->GetX() + $image_width); // Move to the right of the image
        $pdf->Cell($image_width, 30, 'Image not found', 0, 0, 'L');
    }

    // Move to the right of the image
    $pdf->SetX($pdf->GetX() + $image_width + $text_margin);

    // Print product details
    $pdf->Cell(0, 10, $item['product_name'] . ' (' . $item['color'] . ', ' . $item['size'] . ')', 0, 1, "R");
    $pdf->Cell(0, 10, 'Price: $' . number_format($item['price'], 2), 0, 1, "R");
    $pdf->Cell(0, 10, 'Quantity: ' . $item['quantity'], 0, 1, "R");
    $pdf->Cell(0, 10, 'Total: $' . number_format($item['quantity'] * $item['price'], 2), 0, 1, "R");

    // Move to the next line after each item
    $pdf->Ln(10);
}

// Output PDF
$pdf->Output('I', 'Invoice_' . $order['order_id'] . '.pdf');
<?php
session_start();
include '../database/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "usr_login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$address = $_POST['address'];
$city = $_POST['city'];
$province = $_POST['province'];
$postal_code = $_POST['postal_code'];
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;

// Fetch cart items
$sql_cart = "SELECT 
                c.quantity, 
                pv.price, 
                pv.product_image, 
                p.product_name, 
                pv.variant_id,
                p.seller_id
             FROM cart c
             LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id
             LEFT JOIN products p ON pv.product_id = p.product_id
             WHERE c.user_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param('i', $user_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

$cart_items = [];
$total_price = 0;

if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['quantity'] * $row['price'];
    }
}

$taxes = $total_price * 0.13;

// Use the discounted total amount if it is set, otherwise use the full total amount
$total_amount = isset($_SESSION['cart_total_amount']) ? $_SESSION['cart_total_amount'] : ($total_price + $taxes);

// Insert order into orders table
$sql_order = "INSERT INTO orders (user_id, total_amount, shipping_status, created_at, shipping_address, city, province, postal_code, phone_number) VALUES (?, ?, 'Pending', NOW(), ?, ?, ?, ?, ?)";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param('idsssss', $user_id, $total_amount, $address, $city, $province, $postal_code, $phone);
$stmt_order->execute();
$order_id = $stmt_order->insert_id;
$stmt_order->close();

// Insert order items into order_items table
foreach ($cart_items as $item) {
    $total_item_price = $item['quantity'] * $item['price'];
    $sql_order_item = "INSERT INTO order_items (order_id, seller_id, variant_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order_item = $conn->prepare($sql_order_item);
    $stmt_order_item->bind_param('iiiddd', $order_id, $item['seller_id'], $item['variant_id'], $item['quantity'], $item['price'], $total_item_price);
    $stmt_order_item->execute();
    $stmt_order_item->close();
}

// Clear the session variable after checkout
unset($_SESSION['cart_total_amount']);

// Redirect to PayPal checkout page 
header("Location: create_payment.php?order_id=$order_id");
exit();
?>

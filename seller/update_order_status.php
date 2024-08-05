<?php
include '../database/conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];
$current_status = $data['current_status'];

$status_map = [
    'Pending' => 'Unshipped',
    'Unshipped' => 'Shipped',
    'Shipped' => 'Delivered'
];

$new_status = $status_map[$current_status] ?? null;

if ($new_status) {
    $stmt = $conn->prepare("UPDATE orders SET shipping_status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $new_status, $order_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
?>

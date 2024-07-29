<?php
session_start();
include 'navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $logged_in = false;
} else {
    $logged_in = true;
    $user_id = $_SESSION['user_id'];

    // Fetch user orders
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
                 WHERE o.user_id = ?
                 ORDER BY o.created_at DESC";
    $stmt_orders = $conn->prepare($sql_orders);

    if ($stmt_orders === false) {
        die('Error preparing the statement: ' . $conn->error);
    }

    $stmt_orders->bind_param('i', $user_id);
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();

    $orders = [];
    while ($row = $result_orders->fetch_assoc()) {
        $orders[$row['order_id']]['created_at'] = $row['created_at'];
        $orders[$row['order_id']]['total_amount'] = $row['total_amount'];
        $orders[$row['order_id']]['shipping_status'] = $row['shipping_status'];
        $orders[$row['order_id']]['items'][] = $row;
    }

    $stmt_orders->close();
}
?>

<main class="container my-5">
    <h2 class="fw-bold mb-4 shop-pg-search-title">My Orders</h2>
    <?php if (!$logged_in) : ?>
        <div class="text-center">
            <img src="../assets/img/empty-cart-sad.svg" class="img-fluid mb-4 empty-cart-img" alt="Access Denied">
            <h3 class="fw-bold mt-4">You need to <a href="usr_login.php">Login</a> or <a href="usr_register.php">Signup</a> to view your orders</h3>
        </div>
    <?php elseif (!empty($orders)) : ?>
        <?php foreach ($orders as $order_id => $order) : ?>
            <?php
            // Determine the class based on shipping status
            switch ($order['shipping_status']) {
                case 'Pending':
                    $status_class = 'text-primary';
                    break;
                case 'Shipped':
                    $status_class = 'text-warning';
                    break;
                case 'Delivered':
                    $status_class = 'text-success';
                    break;
                default:
                    $status_class = 'text-muted';
                    break;
            }
            ?>
            <div class="card mb-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between">
                    <div>
                        <h5 class="order-id-text fw-bold">Order ID: <?php echo $order_id; ?></h5>
                        <p class="mb-1">Order Date: <?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
                        <p class="mb-0 fw-bold">Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    <div class="text-md-end">
                        <p class="mb-0">Shipping Status: <span class="<?php echo $status_class; ?>"><?php echo ucfirst($order['shipping_status']); ?></span></p>
                        <a href="generate_invoice.php?order_id=<?php echo $order_id; ?>" target="_blank" class="btn btn-dark mt-2 rounded-0 invoice-print-btn"><i class="bi bi-printer"></i>&nbsp; Invoice</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php 
                    $item_count = count($order['items']);
                    $counter = 1;
                    ?>
                    <?php foreach ($order['items'] as $item) : ?>
                        <div class="d-flex flex-column flex-md-row justify-content-between mt-3">
                            <div class="d-flex align-items-center">
                                <img src="../uploads/<?php echo $item['product_image']; ?>" class="order-pd-img" alt="<?php echo $item['product_name']; ?>">
                                <div class="ms-3">
                                    <h6 class="orders-pd-name"><?php echo $item['product_name']; ?> (<?php echo $item['color']; ?>, <?php echo $item['size']; ?>)</h6>
                                    <p class="mb-0">Price: $<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="mb-0">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                            <div class="text-md-end mt-2 mt-md-0">
                                <p class="fw-bold mb-0">$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></p>
                            </div>
                        </div>
                        <?php if ($counter < $item_count) : ?>
                            <hr>
                        <?php endif; ?>
                        <?php $counter++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="text-center">
            <img src="../assets/img/empty-cart-sad.svg" class="img-fluid mb-4 empty-cart-img" alt="Empty Orders">
            <h3 class="fw-bold mt-4">You have no orders</h3>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

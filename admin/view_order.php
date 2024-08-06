<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Get the order ID from the URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no valid order ID is provided, redirect to the orders page
if ($order_id <= 0) {
    header("Location: ad_orders.php");
    exit;
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch order details
$query = "SELECT 
                o.order_id, 
                p.product_name, 
                sz.size_name, 
                clr.color_name, 
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
                o.shipping_address, o.city, o.province, o.postal_code, o.phone_number,
                s.store_name,
                o.shipping_status,
                o.created_at,
                o.updated_at,
                v.price,
                v.product_image,
                oi.quantity,
                oi.total_price
          FROM orders o
          LEFT JOIN order_items oi ON o.order_id = oi.order_id
          LEFT JOIN product_variants v ON oi.variant_id = v.variant_id
          LEFT JOIN products p ON v.product_id = p.product_id
          LEFT JOIN colors clr ON v.color_id = clr.color_id
          LEFT JOIN sizes sz ON v.size_id = sz.size_id
          LEFT JOIN users u ON o.user_id = u.user_id
          LEFT JOIN sellers s ON p.seller_id = s.seller_id
          WHERE o.order_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$order_details = [];
while ($row = $result->fetch_assoc()) {
    $order_details[] = $row;
}

if (empty($order_details)) {
    echo "<main id='main-admin' class='main-admin'><div class='container-fluid'><div class='row mb-3'><div class='col-lg-12'><h3 class='slr-product-page-title'>Order not found.</h3></div></div></div></main>";
    include 'footer.php';
    exit;
}

$colors = ['#FF0000', '#0000FF', '#FF00FF', '#00FF00', '#00FFFF', '#FFFF00'];
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Order Detail</h3>
                <a href="ad_orders.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Orders</a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Order ID: <?php echo htmlspecialchars($order_details[0]['order_id']); ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fs-5 text-primary fw-bold">Customer Details</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details[0]['customer_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_details[0]['phone_number']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($order_details[0]['shipping_address']) . ", " . htmlspecialchars($order_details[0]['city']) . ", " . htmlspecialchars($order_details[0]['province']) . ", " . htmlspecialchars($order_details[0]['postal_code']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fs-5 text-primary fw-bold">Order Details</h6>
                            <p><strong>Shipping Status:</strong> <?php echo htmlspecialchars($order_details[0]['shipping_status']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_details[0]['created_at']); ?></p>
                            <p><strong>Last Update:</strong> <?php echo htmlspecialchars($order_details[0]['updated_at']); ?></p>
                        </div>
                    </div>

                    <h6 class="fs-5 text-primary fw-bold">Order Items</h6>
                    <?php foreach ($order_details as $index => $item) : ?>
                        <?php $color = $colors[$index % count($colors)]; ?>
                        <div class="order-item" style="border: 1px solid <?php echo $color; ?>; margin-bottom: 1rem; padding: 1rem;">
                            <img src="../uploads/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 100px; height: 100px; object-fit: cover;">
                            <p><strong>Product:</strong> <?php echo htmlspecialchars($item['product_name']); ?></p>
                            <p><strong>Size:</strong> <?php echo htmlspecialchars($item['size_name']); ?></p>
                            <p><strong>Color:</strong> <?php echo htmlspecialchars($item['color_name']); ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p><strong>Total Price:</strong> $<?php echo number_format($item['total_price'], 2); ?></p>
                            <p><strong>Seller:</strong> <?php echo htmlspecialchars($item['store_name']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
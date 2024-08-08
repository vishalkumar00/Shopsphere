<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Get the user ID from the URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no valid user ID is provided, redirect to the users page
if ($user_id <= 0) {
    header("Location: users_list.php");
    exit;
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch user details
$userQuery = "SELECT 
                CONCAT(first_name, ' ', last_name) AS full_name, 
                email, 
                mobile_number, 
                created_at 
              FROM users 
              WHERE user_id = ?";

$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $user_id);

if ($userStmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$userStmt->execute();
$userResult = $userStmt->get_result();
$userStmt->close();

$userDetails = $userResult->fetch_assoc();

if (empty($userDetails)) {
    echo "<main id='main-admin' class='main-admin'><div class='container-fluid'><div class='row mb-3'><div class='col-lg-12'><h3 class='slr-product-page-title'>User not found.</h3></div></div></div></main>";
    include 'footer.php';
    exit;
}

// Fetch user orders
$orderQuery = "SELECT 
                o.order_id, 
                o.created_at, 
                o.shipping_status,
                oi.quantity,
                oi.total_price,
                p.product_name,
                v.price,
                v.product_image,
                clr.color_name,
                sz.size_name,
                s.store_name
              FROM orders o
              LEFT JOIN order_items oi ON o.order_id = oi.order_id
              LEFT JOIN product_variants v ON oi.variant_id = v.variant_id
              LEFT JOIN products p ON v.product_id = p.product_id
              LEFT JOIN colors clr ON v.color_id = clr.color_id
              LEFT JOIN sizes sz ON v.size_id = sz.size_id
              LEFT JOIN sellers s ON p.seller_id = s.seller_id
              WHERE o.user_id = ?";

$orderStmt = $conn->prepare($orderQuery);
$orderStmt->bind_param("i", $user_id);

if ($orderStmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$orderStmt->close();

$orders = [];
while ($row = $orderResult->fetch_assoc()) {
    $orders[] = $row;
}

$colors = ['#FF0000', '#0000FF', '#FF00FF', '#00FF00', '#00FFFF', '#FFFF00'];
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">User Details</h3>
                <a href="users_list.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Users</a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">User ID: <?php echo htmlspecialchars($user_id); ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fs-5 text-primary fw-bold">Customer Details</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($userDetails['full_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($userDetails['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($userDetails['mobile_number']); ?></p>
                            <p><strong>Registered At:</strong> <?php echo htmlspecialchars($userDetails['created_at']); ?></p>
                        </div>
                    </div>

                    <h6 class="fs-5 text-primary fw-bold">Order Details</h6>
                    <?php if (!empty($orders)) : ?>
                        <?php foreach ($orders as $index => $order) : ?>
                            <?php $color = $colors[$index % count($colors)]; ?>
                            <div class="order-item" style="border: 1px solid <?php echo $color; ?>; margin-bottom: 1rem; padding: 1rem;">
                                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                                <p><strong>Shipping Status:</strong> <?php echo htmlspecialchars($order['shipping_status']); ?></p>
                                <img src="../uploads/<?php echo htmlspecialchars($order['product_image']); ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>" style="width: 100px; height: 100px; object-fit: cover;">
                                <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                                <p><strong>Size:</strong> <?php echo htmlspecialchars($order['size_name']); ?></p>
                                <p><strong>Color:</strong> <?php echo htmlspecialchars($order['color_name']); ?></p>
                                <p><strong>Price:</strong> $<?php echo number_format($order['price'], 2); ?></p>
                                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
                                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                                <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['store_name']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No orders found for this user.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
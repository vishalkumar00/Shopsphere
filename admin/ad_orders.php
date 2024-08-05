<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Get the selected shipping status from the dropdown
$selected_status = isset($_GET['shipping_status']) ? $_GET['shipping_status'] : '';

// Modify the query to filter by the selected shipping status if set
$query = "SELECT 
                o.order_id, 
                p.product_name, 
                sz.size_name, 
                clr.color_name, 
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
                o.shipping_address, o.city, o.province, o.postal_code, o.phone_number,
                s.store_name,
                o.shipping_status
          FROM orders o
          LEFT JOIN order_items oi ON o.order_id = oi.order_id
          LEFT JOIN product_variants v ON oi.variant_id = v.variant_id
          LEFT JOIN products p ON v.product_id = p.product_id
          LEFT JOIN colors clr ON v.color_id = clr.color_id
          LEFT JOIN sizes sz ON v.size_id = sz.size_id
          LEFT JOIN users u ON o.user_id = u.user_id
          LEFT JOIN sellers s ON p.seller_id = s.seller_id";
          
if ($selected_status != '') {
    $query .= " WHERE o.shipping_status = ?";
}

$query .= " ORDER BY o.order_id, oi.variant_id";

$stmt = $conn->prepare($query);

if ($selected_status != '') {
    $stmt->bind_param("s", $selected_status);
}

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']][] = $row;
}

$colors = ['#FF0000', '#0000FF', '#FF00FF', '#00FF00', '#00FFFF', '#FFFF00'];
$colorIndex = 0;
?>

<main id="main-admin" class="main-admin">

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Orders</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                <div class="col-lg-12 d-flex justify-content-between align-items-center">
                    <h5 class="card-title category-card-title">Placed Orders</h5>
                    <form method="GET" action="ad_orders.php" class="form-inline">
                        <div class="d-flex">
                    <label for="shipping_status">Filter by Status</label>
                    <select name="shipping_status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php if ($selected_status == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Unshipped" <?php if ($selected_status == 'Unshipped') echo 'selected'; ?>>Unshipped</option>
                        <option value="Shipped" <?php if ($selected_status == 'Shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="Delivered" <?php if ($selected_status == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    </select>
                    </div>
                </form>
                </div> 
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Order Items</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Seller Names</th>
                                    <th scope="col">Shipping Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0) : ?>
                                    <?php foreach ($orders as $order_id => $items) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order_id); ?></td>
                                            <td>
                                                <?php foreach ($items as $index => $item) : ?>
                                                    <?php $color = $colors[$index % count($colors)]; ?>
                                                    <span style="color: <?php echo $color; ?>"><?php echo htmlspecialchars($item['product_name']) . ' (' . htmlspecialchars($item['size_name']) . ', ' . htmlspecialchars($item['color_name']) . ')'; ?></span><br>
                                                <?php endforeach; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($items[0]['customer_name']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($items[0]['shipping_address']) . ", " . htmlspecialchars($items[0]['city']) . ", " . htmlspecialchars($items[0]['province']) . ", " . htmlspecialchars($items[0]['postal_code']); ?><br>
                                                <b>Phone: </b><?php echo htmlspecialchars($items[0]['phone_number']); ?>
                                            </td>
                                            <td>
                                                <?php foreach ($items as $index => $item) : ?>
                                                    <?php $color = $colors[$index % count($colors)]; ?>
                                                    <span style="color: <?php echo $color; ?>"><?php echo htmlspecialchars($item['store_name']); ?></span><br>
                                                <?php endforeach; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($items[0]['shipping_status']); ?></td>
                                            <td>
                                                <div class="tooltip-custom">
                                                    <a href="view_order.php?id=<?php echo $order_id; ?>" class="btn btn-primary btn-sm"><i class="bi bi-journal-text fs-5"></i></a>
                                                    <span class="tooltiptext">Order Details</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

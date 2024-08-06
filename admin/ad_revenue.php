<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Fetch revenue details
$query = "
    SELECT 
        oi.order_item_id,
        p.product_name,
        clr.color_name,
        sz.size_name,
        oi.quantity,
        oi.price,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
        s.store_name,
        (oi.price - (oi.price * (c.commission_rate / 100))) AS seller_revenue,
        ((oi.price * (c.commission_rate / 100))) AS admin_revenue,
        p.product_id,
        o.user_id,
        p.seller_id
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN product_variants v ON oi.variant_id = v.variant_id
    JOIN products p ON v.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    JOIN colors clr ON v.color_id = clr.color_id
    JOIN sizes sz ON v.size_id = sz.size_id
    JOIN users u ON o.user_id = u.user_id
    JOIN sellers s ON p.seller_id = s.seller_id
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Revenue</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Revenue Details</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Order Item ID</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Seller</th>
                                    <th scope="col">Seller's Revenue</th>
                                    <th scope="col">Admin's Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) : ?>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['order_item_id']); ?></td>
                                            <td>
                                                <a href="http://localhost/shopsphere/user/product_details.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>" target="_blank" class="text-primary fw-bold">
                                                    <?php echo htmlspecialchars($row['product_name']) . " (" . htmlspecialchars($row['color_name']) . ", " . htmlspecialchars($row['size_name']) . ")"; ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                                            <td><a href="http://localhost/shopsphere/admin/view_user.php?id=<?php echo htmlspecialchars($row['user_id']); ?>" target="_blank" class="text-primary fw-bold"><?php echo htmlspecialchars($row['customer_name']); ?></a></td>
                                            <td><a href="http://localhost/shopsphere/admin/view_seller.php?seller_id=<?php echo htmlspecialchars($row['seller_id']); ?>" target="_blank" class="text-primary fw-bold"><?php echo htmlspecialchars($row['store_name']); ?></a></td>
                                            <td>$<?php echo number_format($row['seller_revenue'], 2); ?></td>
                                            <td><b class="text-danger">$<?php echo number_format($row['admin_revenue'], 2); ?></b></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8">No revenue details found.</td>
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

<!-- http://localhost/shopsphere/admin/view_user.php?id= -->
<!-- http://localhost/shopsphere/admin/view_seller.php?seller_id= -->
<?php include 'footer.php'; ?>

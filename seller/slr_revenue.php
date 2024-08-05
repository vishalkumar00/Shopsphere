<?php
session_start();
include '../database/conn.php';
include 'header.php';
include 'sidebar.php';

// Check if seller_id is set in session
if (isset($_SESSION['seller_id'])) {
    $sellerId = $_SESSION['seller_id'];

    // Fetch order items and their revenue for the current seller
    $query = "SELECT 
                o.order_id, 
                p.product_name, 
                v.variant_id, 
                v.price AS product_price, 
                c.commission_rate,
                (v.price - (v.price * c.commission_rate / 100)) AS revenue
              FROM order_items oi
              LEFT JOIN orders o ON oi.order_id = o.order_id
              LEFT JOIN product_variants v ON oi.variant_id = v.variant_id
              LEFT JOIN products p ON v.product_id = p.product_id
              LEFT JOIN categories c ON p.category_id = c.category_id
              WHERE p.seller_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        die("An error occurred while fetching order items. Please try again later.");
    }
} else {
    header("Location: login.php");
    exit();
}
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
                    <h5 class="card-title">Order Item Revenues</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Commission (%)</th>
                                    <th scope="col">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) : ?>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                            <td><?php echo "$" . htmlspecialchars($row['product_price']); ?></td>
                                            <td><?php echo htmlspecialchars($row['commission_rate']) . '%'; ?></td>
                                            <td><?php echo "$" . number_format($row['revenue'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">No revenue data available.</td>
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

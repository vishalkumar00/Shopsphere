<?php
session_start();

include 'header.php';
include 'sidebar.php';

// Initialize variables
$sellerId = $_SESSION['seller_id']; 
$revenue = 0.00;

// Fetch total number of variants for the current seller
$variantCountQuery = "SELECT COUNT(*) AS total_variants 
                          FROM product_variants v 
                          JOIN products p ON v.product_id = p.product_id 
                          WHERE p.seller_id = ?";

if ($variantStmt = $conn->prepare($variantCountQuery)) {
  $variantStmt->bind_param("i", $sellerId);
  if (!$variantStmt->execute()) {
    error_log("Execute failed: (" . $variantStmt->errno . ") " . $variantStmt->error);
    die("An error occurred while counting product variants. Please try again later.");
  }
  $variantResult = $variantStmt->get_result();
  $variantCount = $variantResult->fetch_assoc()['total_variants'] ?? 0;
  $variantStmt->close();
} else {
  error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  die("An error occurred while counting product variants. Please try again later.");
}

$ordersCountQuery = "SELECT COUNT(*) AS orders_count
                     FROM orders o
                     JOIN order_items oi ON o.order_id = oi.order_id
                     JOIN product_variants v ON oi.variant_id = v.variant_id
                     JOIN products p ON v.product_id = p.product_id
                     WHERE p.seller_id = ? AND DATE(o.created_at) = CURDATE()";

if ($ordersStmt = $conn->prepare($ordersCountQuery)) {
  $ordersStmt->bind_param("i", $sellerId);
  if (!$ordersStmt->execute()) {
    error_log("Execute failed: (" . $ordersStmt->errno . ") " . $ordersStmt->error);
    die("An error occurred while counting orders. Please try again later.");
  }
  $ordersResult = $ordersStmt->get_result();
  $ordersCount = $ordersResult->fetch_assoc()['orders_count'] ?? 0;
  $ordersStmt->close();
} else {
  error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  die("An error occurred while counting orders. Please try again later.");
}

// Calculate revenue for the past 24 hours for the current seller
$revenueQuery = "SELECT 
                 SUM(oi.price * oi.quantity * ((100 - c.commission_rate) / 100)) AS total_revenue
                 FROM 
                 order_items oi
                 JOIN 
                 orders o ON oi.order_id = o.order_id
                 JOIN 
                 product_variants pv ON oi.variant_id = pv.variant_id
                 JOIN 
                 products p ON pv.product_id = p.product_id
                 JOIN 
                 categories c ON p.category_id = c.category_id
                 WHERE 
                 p.seller_id = ? 
                 AND DATE(o.created_at) = CURDATE()";

if ($revenueStmt = $conn->prepare($revenueQuery)) {
  $revenueStmt->bind_param("i", $sellerId);
  if (!$revenueStmt->execute()) {
    error_log("Execute failed: (" . $revenueStmt->errno . ") " . $revenueStmt->error);
    die("An error occurred while calculating revenue. Please try again later.");
  }
  $revenueResult = $revenueStmt->get_result();
  $revenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0.0; // Corrected key here
  $revenueStmt->close();
} else {
  error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  die("An error occurred while calculating revenue. Please try again later.");
}

// Fetch recent orders for the current seller
$recentOrdersQuery = "SELECT o.order_id, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, p.product_name, p.product_id, oi.price, oi.quantity, o.shipping_status
                      FROM orders o
                      JOIN order_items oi ON o.order_id = oi.order_id
                      JOIN product_variants v ON oi.variant_id = v.variant_id
                      JOIN products p ON v.product_id = p.product_id
                      JOIN users u ON o.user_id = u.user_id
                      WHERE p.seller_id = ?
                      ORDER BY o.created_at DESC
                      LIMIT 5";

if ($recentOrdersStmt = $conn->prepare($recentOrdersQuery)) {
  $recentOrdersStmt->bind_param("i", $sellerId);
  if (!$recentOrdersStmt->execute()) {
    error_log("Execute failed: (" . $recentOrdersStmt->errno . ") " . $recentOrdersStmt->error);
    die("An error occurred while fetching recent orders. Please try again later.");
  }
  $recentOrdersResult = $recentOrdersStmt->get_result();
  $recentOrders = $recentOrdersResult->fetch_all(MYSQLI_ASSOC);
  $recentOrdersStmt->close();
} else {
  error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  die("An error occurred while fetching recent orders. Please try again later.");
}


?>

<main id="main-admin" class="main-admin">

  <div class="pagetitle">
    <h1>Dashboard</h1>
  </div>

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Orders Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Orders <span class="dash-timeline">| Today</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-box"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo htmlspecialchars($ordersCount); ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">
              <div class="card-body">
                <h5 class="card-title">Revenue <span class="dash-timeline">| Today</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo "$" . number_format($revenue, 2); ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Products Card -->
          <div class="col-xxl-4 col-xl-12">
            <div class="card info-card customers-card">
              <div class="card-body">
                <h5 class="card-title">Products & Variants</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo htmlspecialchars($variantCount); ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">
              <div class="card-body">
                <h5 class="card-title">Recent Orders</h5>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Product</th>
                      <th scope="col">Quantity</th> 
                      <th scope="col">Price</th>
                      <th scope="col">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recentOrders as $order) : ?>
                      <tr>
                        <th scope="row"><?php echo htmlspecialchars($order['order_id']); ?></th>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>
                          <a href="http://localhost/shopsphere/user/product_details.php?product_id=<?php echo htmlspecialchars($order['product_id']); ?>" target="_blank" class="text-primary">
                            <?php echo htmlspecialchars($order['product_name']); ?>
                          </a>
                        </td>
                        <td><b><?php echo htmlspecialchars($order['quantity']); ?></b></td> 
                        <td><b><?php echo "$" . number_format($order['price'], 2); ?></b></td>
                        <td>
                          <?php
                          $statusClass = '';
                          switch ($order['shipping_status']) {
                            case 'Pending':
                              $statusClass = 'bg-warning';
                              break;
                            case 'Shipped':
                              $statusClass = 'bg-primary';
                              break;
                            case 'Delivered':
                              $statusClass = 'bg-success';
                              break;
                            case 'Rejected':
                              $statusClass = 'bg-danger';
                              break;
                            default:
                              $statusClass = 'bg-secondary';
                              break;
                          }
                          ?>
                          <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['shipping_status']); ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <div class="col-lg-4">

        <!-- Recent Activity -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Recent Activity <span class="dash-timeline">| Announcements</span></h5>

            <div class="activity">
              <!-- Add recent activities here -->
              <div class="activity-item d-flex">
                <div class="activite-label">32 min</div>
                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                <div class="activity-content">
                  Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae
                </div>
              </div>
            </div>

          </div>
        </div><!-- End Recent Activity -->

      </div><!-- End Right side columns -->

    </div>
  </section>

</main><!-- End #main -->

<?php include 'footer.php'; ?>
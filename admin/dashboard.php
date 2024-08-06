<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// SQL query to count today's orders
$ordersCountQuery = "SELECT COUNT(*) AS today_orders FROM orders WHERE DATE(created_at) = CURDATE()";
$stmt = $conn->prepare($ordersCountQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$today_orders = $row['today_orders'];
$stmt->close();

// SQL query to calculate today's revenue
$revenueQuery = "
    SELECT SUM((order_items.price * categories.commission_rate) / 100) AS today_revenue
    FROM order_items
    JOIN orders ON order_items.order_id = orders.order_id
    JOIN product_variants ON order_items.variant_id = product_variants.variant_id
    JOIN products ON product_variants.product_id = products.product_id
    JOIN categories ON products.category_id = categories.category_id
    WHERE DATE(orders.created_at) = CURDATE()
";
$stmt = $conn->prepare($revenueQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$today_revenue = $row['today_revenue'] ?? 0.00;
$stmt->close();

// SQL query to count total customers
$customersCountQuery = "SELECT COUNT(*) AS total_customers FROM users";
$stmt = $conn->prepare($customersCountQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_customers = $row['total_customers'];
$stmt->close();

// SQL query to count total sellers
$sellersCountQuery = "SELECT COUNT(*) AS total_sellers FROM sellers";
$stmt = $conn->prepare($sellersCountQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_sellers = $row['total_sellers'];
$stmt->close();

// SQL query to get recent orders
$recentOrdersQuery = "
    SELECT orders.order_id, 
           CONCAT(users.first_name, ' ', users.last_name) AS customer_name, 
           sellers.store_name AS seller_name, 
           products.product_name, 
           products.product_id,  -- Include product_id for the link
           order_items.price, 
           order_items.quantity, 
           orders.shipping_status
    FROM order_items
    JOIN orders ON order_items.order_id = orders.order_id
    JOIN product_variants ON order_items.variant_id = product_variants.variant_id
    JOIN products ON product_variants.product_id = products.product_id
    JOIN users ON orders.user_id = users.user_id
    JOIN sellers ON products.seller_id = sellers.seller_id
    WHERE DATE(orders.created_at) = CURDATE()
    ORDER BY orders.created_at DESC
    LIMIT 10
";

$stmt = $conn->prepare($recentOrdersQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// SQL query to get top selling products
$topSellingQuery = "
    SELECT product_variants.product_image, 
           products.product_name, 
           CONCAT(COALESCE(colors.color_name, 'None'), ', ', COALESCE(sizes.size_name, 'None')) AS variant_details,
           order_items.price,
           SUM(order_items.quantity) AS total_sold,
           SUM(order_items.price * order_items.quantity * categories.commission_rate / 100) AS total_revenue,
           products.product_id  -- Ensure product_id is included for the link
    FROM order_items
    JOIN product_variants ON order_items.variant_id = product_variants.variant_id
    JOIN products ON product_variants.product_id = products.product_id
    LEFT JOIN colors ON product_variants.color_id = colors.color_id
    LEFT JOIN sizes ON product_variants.size_id = sizes.size_id
    JOIN categories ON products.category_id = categories.category_id
    GROUP BY product_variants.variant_id
    ORDER BY total_sold DESC 
    LIMIT 5
";
$stmt = $conn->prepare($topSellingQuery);
if ($stmt === false) {
  die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->execute();
$result = $stmt->get_result();
$top_selling = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
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

          <!-- Order Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Orders <span class="dash-timeline">| Today</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $today_orders; ?></h6>
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
                    <h6>$<?php echo number_format($today_revenue, 2); ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Customers Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card customers-card">
              <div class="card-body">
                <h5 class="card-title">Customers</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $total_customers; ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Seller Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sellers-card">
              <div class="card-body">
                <h5 class="card-title">Sellers</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bx bxs-user-detail"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo $total_sellers; ?></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Orders  -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">
              <div class="card-body">
                <h5 class="card-title">Recent Orders <span class="dash-timeline">| Today</span></h5>
                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Seller</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Quantity</th>
                      <th scope="col">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_orders as $order) : ?>
                      <tr>
                        <th scope="row"><?php echo '#' . htmlspecialchars($order['order_id']); ?></th>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                        <td><a href="http://localhost/shopsphere/user/product_details.php?product_id=<?php echo htmlspecialchars($order['product_id']); ?>" target="_blank" class="text-primary"><?php echo htmlspecialchars($order['product_name']); ?></a></td>
                        <td><b>$<?php echo number_format($order['price'], 2); ?></b></td>
                        <td class="text-center"><b><?php echo htmlspecialchars($order['quantity']); ?></b></td>
                        <td><span class="badge bg-<?php echo getShippingStatusClass($order['shipping_status']); ?>"><?php echo htmlspecialchars($order['shipping_status']); ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Top Selling -->
          <div class="col-12">
            <div class="card top-selling overflow-auto">
              <div class="card-body pb-0">
                <h5 class="card-title">Top Selling</h5>
                <table class="table table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Preview</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Sold</th>
                      <th scope="col">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($top_selling as $item) : ?>
                      <tr>
                        <th scope="row">
                          <a href="#"><img src="../uploads/<?php echo htmlspecialchars($item['product_image']); ?>" alt="" style="width: 50px; height: 50px;"></a>
                        </th>
                        <td>
                          <a href="http://localhost/shopsphere/user/product_details.php?product_id=<?php echo htmlspecialchars($item['product_id']); ?>" target="_blank" class="text-primary fw-bold">
                            <?php echo htmlspecialchars($item['product_name']); ?> (<?php echo htmlspecialchars($item['variant_details']); ?>)
                          </a>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($item['total_sold']); ?></td>
                        <td>$<?php echo number_format($item['total_revenue'], 2); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right side columns -->
      <div class="col-lg-4">

        <!-- Announcement -->
        <div class="card">

          <div class="card-body">
            <h5 class="card-title">Announcements</h5>

            <div class="activity">

              <div class="activity-item d-flex">
                <div class="ann-mins"><span>32 min</span></div>
                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                <div class="activity-content">
                  Quia quae rerum explicabo officiis beatae
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">56 min</div>
                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                <div class="activity-content">
                  Voluptatem blanditiis blanditiis eveniet
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">2 hrs</div>
                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                <div class="activity-content">
                  Voluptates corrupti molestias voluptatem
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">1 day</div>
                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                <div class="activity-content">
                  Tempore autem saepe occaecati voluptatem tempore
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">2 days</div>
                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                <div class="activity-content">
                  Est sit eum reiciendis exercitationem
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">4 weeks</div>
                <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                <div class="activity-content">
                  Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>
<?php
function getShippingStatusClass($status)
{
  switch ($status) {
    case 'Approved':
      return 'success';
    case 'Pending':
      return 'warning';
    case 'Rejected':
      return 'danger';
    default:
      return 'secondary';
  }
}
?>
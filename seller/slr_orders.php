<?php
include 'header.php';
include 'sidebar.php';

// Assuming seller_id is stored in the session
$seller_id = $_SESSION['seller_id'];

$query = "SELECT 
                o.order_id, 
                p.product_name, 
                p.product_id, 
                sz.size_name, 
                clr.color_name, 
                oi.quantity, 
                oi.price,
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
          LEFT JOIN sellers s ON p.seller_id = s.seller_id
          WHERE s.seller_id = ?
          ORDER BY o.order_id, oi.variant_id";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $seller_id);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['shipping_status']][$row['order_id']][] = $row;
}
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Orders</h3>
            </div>
        </div>

        <ul class="nav nav-tabs custom-nav-tabs" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link custom-nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link custom-nav-link" id="unshipped-tab" data-bs-toggle="tab" data-bs-target="#unshipped" type="button" role="tab" aria-controls="unshipped" aria-selected="false">Unshipped</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link custom-nav-link" id="shipped-tab" data-bs-toggle="tab" data-bs-target="#shipped" type="button" role="tab" aria-controls="shipped" aria-selected="false">Shipped</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link custom-nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab" aria-controls="delivered" aria-selected="false">Delivered</button>
            </li>
        </ul>

        <div class="tab-content" id="orderTabsContent">
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <?php displayOrdersTable($orders['Pending'] ?? [], 'Pending'); ?>
            </div>
            <div class="tab-pane fade" id="unshipped" role="tabpanel" aria-labelledby="unshipped-tab">
                <?php displayOrdersTable($orders['Unshipped'] ?? [], 'Unshipped'); ?>
            </div>
            <div class="tab-pane fade" id="shipped" role="tabpanel" aria-labelledby="shipped-tab">
                <?php displayOrdersTable($orders['Shipped'] ?? [], 'Shipped'); ?>
            </div>
            <div class="tab-pane fade" id="delivered" role="tabpanel" aria-labelledby="delivered-tab">
                <?php displayOrdersTable($orders['Delivered'] ?? [], 'Delivered'); ?>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';

function displayOrdersTable($orders, $current_status)
{
    echo '<div class="table-responsive">';
    echo '<table class="table datatable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Order ID</th>';
    echo '<th scope="col">Order Items</th>';
    echo '<th scope="col">Quantity</th>';
    echo '<th scope="col">Price</th>';
    echo '<th scope="col">Customer</th>';
    echo '<th scope="col">Address</th>';
    echo '<th scope="col">Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    if (count($orders) > 0) {
        foreach ($orders as $order_id => $items) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($order_id) . '</td>';
            echo '<td>';
            foreach ($items as $item) {
                echo '<a href="http://localhost/shopsphere/user/product_details.php?product_id=' . htmlspecialchars($item['product_id']) . '" target="_blank" class="text-primary">' . htmlspecialchars($item['product_name']) . '</a> (' . htmlspecialchars($item['size_name']) . ', ' . htmlspecialchars($item['color_name']) . ')<br>';
            }
            echo '</td>';
            echo '<td>';
            foreach ($items as $item) {
                echo htmlspecialchars($item['quantity']) . '<br>';
            }
            echo '</td>';
            echo '<td>';
            foreach ($items as $item) {
                echo '$' . htmlspecialchars($item['price']) . '<br>';
            }
            echo '</td>';
            echo '<td>' . htmlspecialchars($items[0]['customer_name']) . '</td>';
            echo '<td>' . htmlspecialchars($items[0]['shipping_address']) . ', ' . htmlspecialchars($items[0]['city']) . ', ' . htmlspecialchars($items[0]['province']) . ', ' . htmlspecialchars($items[0]['postal_code']) . '<br><b>Phone: </b>' . htmlspecialchars($items[0]['phone_number']) . '</td>';
            if ($current_status === 'Delivered') {
                echo '<td>Delivered ✅</td>';
            } else {
                $buttonText = '';
                switch ($current_status) {
                    case 'Pending':
                        $buttonText = 'Move to Unshipped 📦';
                        break;
                    case 'Unshipped':
                        $buttonText = 'Move to Shipped 🚚';
                        break;
                    case 'Shipped':
                        $buttonText = 'Move to Delivered 📥';
                        break;
                }
                echo '<td><button class="btn btn-outline-primary change-status-btn" data-order-id="' . htmlspecialchars($order_id) . '" data-current-status="' . htmlspecialchars($current_status) . '">' . $buttonText . '</button></td>';
            }
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No orders found.</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".change-status-btn");

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                const orderId = this.getAttribute("data-order-id");
                const currentStatus = this.getAttribute("data-current-status");

                fetch('update_order_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            current_status: currentStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error updating order status');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
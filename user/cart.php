<?php
session_start();
include 'navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $is_logged_in = false;
} else {
    $is_logged_in = true;
    $user_id = $_SESSION['user_id'];
}

// Fetch cart items only if user is logged in
if ($is_logged_in) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'update_quantity' && isset($_POST['variant_id']) && isset($_POST['quantity'])) {
        $variant_id = $_POST['variant_id'];
        $quantity = $_POST['quantity'];

        $sql_update = "UPDATE cart SET quantity = ? WHERE variant_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param('iii', $quantity, $variant_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
        }

        $stmt->close();
        exit;
    }

    if ($action == 'remove_item' && isset($_POST['variant_id'])) {
        $variant_id = $_POST['variant_id'];

        $sql_remove = "DELETE FROM cart WHERE variant_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql_remove);
        $stmt->bind_param('ii', $variant_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
        }

        $stmt->close();
        exit;
    }

    // Fetch cart items
    $sql_cart = "SELECT 
                    c.quantity, 
                    pv.price, 
                    pv.product_image, 
                    p.product_name, 
                    pv.variant_id, 
                    co.color_name AS color, 
                    si.size_name AS size
                 FROM cart c
                 LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id
                 LEFT JOIN products p ON c.product_id = p.product_id
                 LEFT JOIN colors co ON pv.color_id = co.color_id
                 LEFT JOIN sizes si ON pv.size_id = si.size_id
                 WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql_cart);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result_cart = $stmt->get_result();

    $cart_items = [];
    $total_price = 0;

    if ($result_cart->num_rows > 0) {
        while ($row = $result_cart->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += $row['quantity'] * $row['price'];
        }
    }

    $taxes = $total_price * 0.13;
    $total_amount = $total_price + $taxes;

    $stmt->close();
}
?>

<main class="container-fluid my-5">
    <div class="row">
        <!-- Cart Products Section -->
        <div class="col-md-9 mb-4">
            <h2 class="fw-bold mb-4 shop-pg-search-title">Cart</h2>
            <?php if ($is_logged_in) : ?>
                <?php if (count($cart_items) > 0) : ?>
                    <table class="table table-bordered cart-table">
                        <thead>
                            <tr class="bg-table-initial text-center">
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th>Total</th>
                                <th>Quantity</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <?php foreach ($cart_items as $item) : ?>
                                <tr data-variant-id="<?php echo $item['variant_id']; ?>">
                                    <td><img src="../uploads/<?php echo $item['product_image']; ?>" class="cart-pd-img" alt="<?php echo $item['product_name']; ?>"></td>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td><?php echo $item['color']; ?></td>
                                    <td><?php echo $item['size']; ?></td>
                                    <td class="total-price" data-price="<?php echo $item['price']; ?>">$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                    <td class="quantity-column">
                                        <div class="input-group center-md-pd-details">
                                            <button class="btn quantity-minus rounded-0 quantity-btn" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-subtract-line fw-bold text-white icon-hover"></i></button>
                                            <input type="text" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                            <button class="btn quantity-plus rounded-0 quantity-btn" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-add-line fw-bold text-white"></i></button>
                                        </div>
                                    </td>
                                    <td class="text-center"><button class="btn btn-danger btn-remove rounded-0" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-delete-bin-line"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="text-center">
                        <img src="../assets/img/empty-cart-sad.svg" class="img-fluid mb-4 empty-cart-img" alt="Empty Cart">
                        <p class="fw-bold fs-4">Your cart is empty. <a href="shop.php">Continue Shopping</a></p>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="text-center">
                    <img src="../assets/img/empty-cart-sad.svg" class="img-fluid mb-4 empty-cart-img" alt="Empty Cart">
                    <h3 class="fw-bold">Your cart is empty. Please <a href="usr_login.php">Login</a> or <a href="usr_register.php">Signup</a> to Shop</h3>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cart Summary Section -->
        <div class="col-md-3 summary-section">
            <div class="card p-4 rounded-0 summary-card">
                <h3 class="fw-bold mb-4">Summary</h3>
                <?php if ($is_logged_in && !empty($cart_items)) : ?>
                    <p class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span class="fw-bold" id="totalPrice">$<?php echo number_format($total_price, 2); ?></span>
                    </p>
                    <p class="d-flex justify-content-between">
                        <span>Taxes (13%):</span>
                        <span class="fw-bold" id="taxes">$<?php echo number_format($taxes, 2); ?></span>
                    </p>
                    <hr>
                    <p class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <span class="fw-bold" id="totalAmount">$<?php echo number_format($total_amount, 2); ?></span>
                    </p>
                    <a href="checkout.php" class="btn btn-primary btn-block mt-4 rounded-0 usr-carosuel-btn">Proceed to Checkout</a>
                <?php else : ?>
                    <button type="button" class="btn btn-primary btn-block mt-4 rounded-0 usr-carosuel-btn" disabled>Proceed to Checkout</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
include 'footer.php';
?>

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Quantity Buttons
    $(document).on('click', '.quantity-plus', function() {
        let quantityInput = $(this).siblings('.quantity-input');
        let currentValue = parseInt(quantityInput.val());
        let variantId = $(this).data('variant-id');
        let maxQuantity = 10;

        if (!isNaN(currentValue) && currentValue < maxQuantity) {
            quantityInput.val(currentValue + 1);

            // Send AJAX request to update quantity in the database
            $.ajax({
                url: 'cart.php',
                method: 'POST',
                data: {
                    action: 'update_quantity',
                    variant_id: variantId,
                    quantity: currentValue + 1
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });

    $(document).on('click', '.quantity-minus', function() {
        let quantityInput = $(this).siblings('.quantity-input');
        let currentValue = parseInt(quantityInput.val());
        let variantId = $(this).data('variant-id');

        if (!isNaN(currentValue) && currentValue > 1) {
            quantityInput.val(currentValue - 1);

            // Send AJAX request to update quantity in the database
            $.ajax({
                url: 'cart.php',
                method: 'POST',
                data: {
                    action: 'update_quantity',
                    variant_id: variantId,
                    quantity: currentValue - 1
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });

    // Remove item from cart
    $(document).on('click', '.btn-remove', function() {
        let variantId = $(this).data('variant-id');

        // Send AJAX request to remove item from the cart
        $.ajax({
            url: 'cart.php',
            method: 'POST',
            data: {
                action: 'remove_item',
                variant_id: variantId
            },
            success: function(response) {
                location.reload();
            }
        });
    });
</script>
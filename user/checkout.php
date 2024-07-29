<?php
session_start();
include 'navbar.php';

$user_id = $_SESSION['user_id'] ?? null;
$user = null;
$cart_items = [];
$total_price = 0;
$taxes = 0;
$total_amount = 0;

if ($user_id) {
    // Fetch user details
    $sql_user = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

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
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param('i', $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    if ($result_cart->num_rows > 0) {
        while ($row = $result_cart->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += $row['quantity'] * $row['price'];
        }
    }

    $taxes = $total_price * 0.13;
    $total_amount = $total_price + $taxes;

    $stmt_cart->close();
    $stmt_user->close();
}
?>

<main class="container-fluid my-5">
    <form action="process_checkout.php" method="POST">
        <div class="row">
            <h2 class="fw-bold mb-4 shop-pg-search-title">Checkout</h2>
            <?php if ($user_id && !empty($cart_items)) : ?>
                <!-- Address Information Section -->
                <div class="col-md-8 mb-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?php echo $user['first_name']; ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?php echo $user['last_name']; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="province" class="form-label">Province</label>
                            <select id="province" name="province" class="form-control" required>
                                <option value="" disabled selected>Select Province</option>
                                <option value="AB">Alberta</option>
                                <option value="BC">British Columbia</option>
                                <option value="MB">Manitoba</option>
                                <option value="NB">New Brunswick</option>
                                <option value="NL">Newfoundland and Labrador</option>
                                <option value="NS">Nova Scotia</option>
                                <option value="NT">Northwest Territories</option>
                                <option value="NU">Nunavut</option>
                                <option value="ON">Ontario</option>
                                <option value="PE">Prince Edward Island</option>
                                <option value="QC">Quebec</option>
                                <option value="SK">Saskatchewan</option>
                                <option value="YT">Yukon</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Empty Cart Message Section -->
                <div class="col-md-8 pt-lg-4 text-center">
                    <img src="../assets/img/empty-cart-sad.svg" alt="Empty Cart" class="img-fluid empty-cart-img mb-4">
                    <h2 class="fw-bold mb-2 fs-3 shop-pg-search-title">Your cart is empty</h2>
                    <p>Please add items to your cart to proceed with the checkout.</p>
                </div>
            <?php endif; ?>

            <!-- Order Summary Section -->
            <div class="col-md-4 summary-section">
                <div class="card p-4 rounded-0 summary-card">
                    <h3 class="fw-bold mb-4">Order Summary</h3>
                    <?php if ($user_id && !empty($cart_items)) : ?>
                        <?php foreach ($cart_items as $item) : ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex flex-column">
                                    <div class="position-relative">   
                                        <img src="../uploads/<?php echo $item['product_image']; ?>" class="checkout-pd-img" alt="<?php echo $item['product_name']; ?>">
                                        <span class="badge bg-primary badge-number-3"><?php echo $item['quantity']; ?></span>
                                        <span class="checkout-pd-name"><?php echo $item['product_name']; ?> (<?php echo $item['color']; ?>, <?php echo $item['size']; ?>)</span>
                                    </div>
                                    <div class="checkout-summary-price">
                                        <span>$<?php echo number_format($item['price'], 2); ?></span>
                                    </div>  
                                </div>
                                <div class="pt-2">
                                    <span class="fw-bold">$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
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
                        <button type="submit" class="btn btn-primary btn-block mt-4 rounded-0 usr-carosuel-btn">Proceed to PayPal</button>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary btn-block mt-4 rounded-0 usr-carosuel-btn" disabled>Proceed to PayPal</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</main>

<?php
include 'footer.php';
?>

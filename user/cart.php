<?php
session_start();
include 'navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "usr_login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle AJAX request to update quantity
if (isset($_POST['action']) && $_POST['action'] == 'update_quantity' && isset($_POST['variant_id']) && isset($_POST['quantity'])) {
    $variant_id = $_POST['variant_id'];
    $quantity = $_POST['quantity'];

    // Update the quantity in the cart
    $sql_update = "UPDATE cart SET quantity = ? WHERE variant_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('iii', $quantity, $variant_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    $stmt->close();
    exit;
}

// Handle AJAX request to remove item from cart
if (isset($_POST['action']) && $_POST['action'] == 'remove_item' && isset($_POST['variant_id'])) {
    $variant_id = $_POST['variant_id'];

    // Remove the item from the cart
    $sql_remove = "DELETE FROM cart WHERE variant_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_remove);
    $stmt->bind_param('ii', $variant_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    $stmt->close();
    exit;
}

// Fetch cart items
$sql_cart = "SELECT c.quantity, pv.price, pv.product_image, p.product_name, pv.variant_id
             FROM cart c
             LEFT JOIN product_variants pv ON c.variant_id = pv.variant_id
             LEFT JOIN products p ON c.product_id = p.product_id
             WHERE c.user_id = $user_id";
$result_cart = $conn->query($sql_cart);

$cart_items = [];
$total_price = 0;

if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['quantity'] * $row['price'];
    }
}
?>

<main class="container-fluid my-5">
    <div class="container-lg container-fluid">
        <div class="row">
            <!-- Cart Products Section -->
            <div class="col-md-8 mb-4">
                <h2 class="fw-bold mb-4 shop-pg-search-title">Your Cart</h2>
                <?php if (count($cart_items) > 0) : ?>
                    <table class="table table-bordered cart-table">
                        <thead>
                            <tr class="bg-table-initial text-center">
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <?php foreach ($cart_items as $item) : ?>
                                <tr data-variant-id="<?php echo $item['variant_id']; ?>">
                                    <td><img src="../uploads/<?php echo $item['product_image']; ?>" class="img-fluid product-img" alt="<?php echo $item['product_name']; ?>"></td>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td>
                                        <div class="input-group">
                                            <button class="btn quantity-minus rounded-0 quantity-btn" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-subtract-line fw-bold text-white icon-hover"></i></button>
                                            <input type="text" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                            <button class="btn quantity-plus rounded-0 quantity-btn" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-add-line fw-bold text-white"></i></button>
                                        </div>
                                    </td>
                                    <td class="total-price" data-price="<?php echo $item['price']; ?>">$<?php echo $item['quantity'] * $item['price']; ?></td>
                                    <td class="text-center"><button class="btn btn-danger btn-remove rounded-0" data-variant-id="<?php echo $item['variant_id']; ?>"><i class="ri-delete-bin-line"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <!-- Cart Summary Section -->
            <div class="col-md-4 summary-section">
                <div class="card p-4 rounded-0 summary-card">
                    <h3 class="fw-bold mb-4">Summary</h3>
                    <p class="d-flex justify-content-between">
                        <span>Total Price:</span>
                        <span class="fw-bold" id="totalPrice">$<?php echo $total_price; ?></span>
                    </p>
                    <!-- Optionally add other summary details like taxes, shipping, etc. -->
                    <a href="checkout.php" class="btn btn-primary btn-block mt-4">Proceed to Checkout</a>
                </div>
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
    // Update total price function
    function updateTotalPrice() {
        let totalPrice = 0;
        document.querySelectorAll('.total-price').forEach(function(element) {
            totalPrice += parseFloat(element.textContent.replace('$', ''));
        });
        document.getElementById('totalPrice').textContent = '$' + totalPrice.toFixed(2);
    }

    // Quantity Buttons
    $(document).on('click', '.quantity-plus', function() {
        let quantityInput = $(this).siblings('.quantity-input');
        let currentValue = parseInt(quantityInput.val());
        let variantId = $(this).data('variant-id');
        let maxQuantity = 10; // Set a maximum quantity limit

        if (!isNaN(currentValue) && currentValue < maxQuantity) {
            quantityInput.val(currentValue + 1);

            // Update total price in table
            let price = parseFloat($(this).closest('tr').find('.total-price').data('price'));
            $(this).closest('tr').find('.total-price').text('$' + (price * (currentValue + 1)).toFixed(2));

            // Update total price
            updateTotalPrice();

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
                    if (response.status === 'success') {
                        console.log('Quantity updated');
                    } else {
                        console.log('Error updating quantity');
                    }
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

            // Update total price in table
            let price = parseFloat($(this).closest('tr').find('.total-price').data('price'));
            $(this).closest('tr').find('.total-price').text('$' + (price * (currentValue - 1)).toFixed(2));

            // Update total price
            updateTotalPrice();

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
                    if (response.status === 'success') {
                        console.log('Quantity updated');
                    } else {
                        console.log('Error updating quantity');
                    }
                }
            });
        }
    });

    $(document).on('click', '.btn-remove', function() {
        let variantId = $(this).data('variant-id');
        let row = $(this).closest('tr');

        // Send AJAX request to remove item from the database
        $.ajax({
            url: 'cart.php',
            method: 'POST',
            data: {
                action: 'remove_item',
                variant_id: variantId
            },
            success: function(response) {
                try {
                    let responseData = JSON.parse(response);
                    if (responseData.status === 'success') {
                        // Remove row from table
                        row.remove();

                        // Update total price
                        updateTotalPrice();
                    } else {
                        console.log('Error: ' + responseData.message); // Log the error message
                    }
                } catch (error) {
                    console.log('Error parsing JSON response: ' + error); // Log parsing errors
                }
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error); // Log AJAX request errors
            }
        });
    });
</script>
<?php
session_start();
include 'navbar.php';

if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo "Product ID is required.";
    exit;
}

$product_id = intval($_GET['product_id']);

$sql_product = "SELECT p.product_name, p.description, p.category_id, v.variant_id, v.color_id, v.size_id, v.quantity, v.price, v.product_image, c.color_name, c.color_code, s.size_name, cat.category_name
                FROM products p
                LEFT JOIN product_variants v ON p.product_id = v.product_id
                LEFT JOIN colors c ON v.color_id = c.color_id
                LEFT JOIN sizes s ON v.size_id = s.size_id
                LEFT JOIN categories cat ON p.category_id = cat.category_id
                WHERE p.product_id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result_product = $stmt->get_result();

if ($result_product->num_rows == 0) {
    echo "Product not found.";
    exit;
}

$product = [];
$colors = [];
$sizes = [];
$variants = [];

while ($row = $result_product->fetch_assoc()) {
    $product['product_name'] = $row['product_name'];
    $product['description'] = $row['description'];
    $product['category_id'] = $row['category_id'];
    $product['category_name'] = $row['category_name'];
    $product['variants'][] = $row;
    $colors[$row['color_id']] = [
        'color_name' => $row['color_name'],
        'color_code' => $row['color_code'],
        'product_image' => $row['product_image'],
        'price' => $row['price']
    ];
    $sizes[$row['size_id']] = $row['size_name'];
    $variants[$row['color_id']][$row['size_id']] = [
        'variant_id' => $row['variant_id'],
        'quantity' => $row['quantity'],
        'size_name' => $row['size_name']
    ];
}

$similarProducts = [];
if (!empty($product['category_id'])) {
    $sql_similar_products = "SELECT DISTINCT p.product_id, p.product_name, p.category_id, pv.price, pv.product_image
                            FROM products p
                            LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                            WHERE p.category_id = ? AND p.product_id != ?
                            GROUP BY p.product_id
                            LIMIT 6";

    $stmt_similar_products = $conn->prepare($sql_similar_products);
    $stmt_similar_products->bind_param("ii", $product['category_id'], $product_id);
    $stmt_similar_products->execute();
    $result_similar_products = $stmt_similar_products->get_result();

    if ($result_similar_products->num_rows > 0) {
        while ($row = $result_similar_products->fetch_assoc()) {
            $similarProducts[] = $row;
        }
    }
    $stmt_similar_products->close();
}

$stmt->close();
?>

<main class="container-fluid my-5">
    <div class="container-lg container-fluid">
        <div class="row">
            <div class="col-md-12" id="alertContainer">
                <?php
                if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                        $_SESSION['success_message'] .
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' .
                        '</div>';
                    unset($_SESSION['success_message']);
                }
                ?>
            </div>

            <div class="col-md-6 text-center mb-3 mb-md-0">
                <img src="../uploads/<?php echo $product['variants'][0]['product_image']; ?>" class="img-fluid fixed-height" alt="<?php echo $product['product_name']; ?>" id="productImage">
            </div>
            <div class="col-md-6">
                <h2 class="pd-details-h2 fw-bold mb-0 text-sm-center text-lg-start text-md-start text-xs-center"><?php echo $product['product_name']; ?></h2>
                <small class="text-muted d-block text-sm-center text-lg-start text-md-start text-xs-center">Category: <span class="text-primary"><?php echo $product['category_name']; ?></span></small>
                <p class="pd-details-price fw-bold fs-3 mt-2 text-sm-center text-lg-start text-md-start text-xs-center">$<?php echo $product['variants'][0]['price']; ?></p>
                <form id="addToCartForm">
                    <div class="form-group center-md-pd-details-2">
                        <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Colors</h5>
                        <select class="form-control w-50 mb-3" id="colorSelect" name="color_id" required>
                            <option value="">Select Color</option>
                            <?php foreach ($colors as $color_id => $color) : ?>
                                <option value="<?php echo $color_id; ?>" data-image="../uploads/<?php echo $color['product_image']; ?>" data-price="<?php echo $color['price']; ?>"><?php echo $color['color_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group center-md-pd-details-2">
                        <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Sizes</h5>
                        <select class="form-control w-50 mb-3" id="sizeSelect" name="size_id" required>
                            <option value="">Select Size</option>
                        </select>
                    </div>
                    <div id="quantitySection" class="form-group center-md-pd-details">
                        <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Quantity</h5>
                        <div class="input-group mb-2 center-md-pd-details">
                            <button type="button" class="btn btn-outline-primary rounded-0" id="decreaseQuantity"><i class="ri-subtract-line fw-bold text-white icon-hover"></i></button>
                            <input type="number" class="form-control text-center quantity-input" id="quantity" name="quantity" value="1" min="1" readonly>
                            <button type="button" class="btn btn-outline-primary rounded-0" id="increaseQuantity"><i class="ri-add-line fw-bold text-white"></i></button>
                        </div>
                        <div id="maxQuantityMessage" class="text-danger fw-bold my-2" style="display: none;">Maximum Quantity Reached</div>
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="variant_id" id="variantId" value="">
                    <input type="hidden" name="price" id="price" value="">
                    <div class="center-md-pd-details mt-2" id="addToCartButton">
                        <button type="submit" class="btn btn-primary rounded-0">Add to Cart</button>
                    </div>
                    <div id="outOfStockMessage" class="text-danger fw-bold mt-3 h2 text-sm-center text-lg-start text-md-start text-xs-center" style="display: none;">Out of Stock</div>
                </form>
                <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center mt-4">Description</h5>
                <p class="mt-0 pd-details-desc text-sm-center text-lg-start text-md-start text-xs-center"><?php echo $product['description']; ?></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You must be logged in to add items to the cart.
                </div>
                <div class="modal-footer">
                    <a href="usr_login.php" class="btn btn-primary">Login</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-5">
        <?php if (!empty($similarProducts)) : ?>
            <div class="bg-white pt-5">
                <h2 class="fw-bold text-center usr-feature-crousel-heading mb-3">Similar Products</h2>
                <div class="row">
                    <div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" data-interval="1000">
                        <div class="MultiCarousel-inner">
                            <?php foreach ($similarProducts as $similarProduct) : ?>
                                <div class="item">
                                    <div class="pad15 rounded-2">
                                        <img src="../uploads/<?php echo $similarProduct['product_image']; ?>" class="product-img" alt="<?php echo $similarProduct['product_name']; ?>">
                                        <p class="product-title"><?php echo $similarProduct['product_name']; ?></p>
                                        <p class="product-price fw-bold">$<?php echo $similarProduct['price']; ?></p>
                                        <a href="product_details.php?product_id=<?php echo $similarProduct['product_id']; ?>" class="product-link rounded-0 usr-carosuel-btn btn btn-primary">View Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-primary leftLst"><i class="ri-arrow-left-s-line fw-bold text-white icon-hover"></i></button>
                        <button class="btn btn-primary rightLst"><i class="ri-arrow-right-s-line fw-bold text-white icon-hover"></i></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorSelect = document.getElementById('colorSelect');
        const sizeSelect = document.getElementById('sizeSelect');
        const productImage = document.getElementById('productImage');
        const priceDisplay = document.querySelector('.pd-details-price');
        const quantityInput = document.getElementById('quantity');
        const increaseQuantityButton = document.getElementById('increaseQuantity');
        const decreaseQuantityButton = document.getElementById('decreaseQuantity');
        const addToCartForm = document.getElementById('addToCartForm');
        const variantIdInput = document.getElementById('variantId');
        const priceInput = document.getElementById('price');
        const outOfStockMessage = document.getElementById('outOfStockMessage');
        const maxQuantityMessage = document.getElementById('maxQuantityMessage');
        const addToCartButton = document.getElementById('addToCartButton');
        const quantitySection = document.getElementById('quantitySection')

        let variants = <?php echo json_encode($variants); ?>;

        colorSelect.addEventListener('change', function() {
            const selectedColorId = this.value;
            if (selectedColorId) {
                const selectedColor = this.options[this.selectedIndex];
                productImage.src = selectedColor.getAttribute('data-image');
                priceDisplay.textContent = `$${selectedColor.getAttribute('data-price')}`;

                // Populate sizes based on the selected color
                sizeSelect.innerHTML = '<option value="">Select Size</option>';
                for (const sizeId in variants[selectedColorId]) {
                    const sizeName = variants[selectedColorId][sizeId].size_name;
                    const option = document.createElement('option');
                    option.value = sizeId;
                    option.textContent = sizeName;
                    sizeSelect.appendChild(option);
                }
            }
        });

        sizeSelect.addEventListener('change', function() {
            const selectedColorId = colorSelect.value;
            const selectedSizeId = this.value;
            if (selectedSizeId) {
                const variant = variants[selectedColorId][selectedSizeId];
                variantIdInput.value = variant.variant_id;
                priceInput.value = variant.price;

                // Update quantity input based on the selected variant's quantity
                const maxAvailableQuantity = Math.min(10, variant.quantity);
                quantityInput.max = maxAvailableQuantity;
                quantityInput.value = 1;

                // Show or hide the out-of-stock message
                if (variant.quantity <= 0) {
                    outOfStockMessage.style.display = 'block';
                    quantitySection.style.display = 'none';
                    addToCartButton.style.display = 'none';
                } else {
                    outOfStockMessage.style.display = 'none';
                    quantitySection.style.display = 'block';
                    addToCartButton.style.display = 'block';
                }
            }
        });

        increaseQuantityButton.addEventListener('click', function() {
            const currentQuantity = parseInt(quantityInput.value);
            const maxQuantity = parseInt(quantityInput.max);
            if (currentQuantity < maxQuantity) {
                quantityInput.value = currentQuantity + 1;
                maxQuantityMessage.style.display = 'none';
            } else {
                maxQuantityMessage.style.display = 'block';
            }
        });

        decreaseQuantityButton.addEventListener('click', function() {
            const currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
                maxQuantityMessage.style.display = 'none';
            }
        });

        function updateCartCount() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'cart_count.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const cartCount = xhr.responseText;
                    document.querySelector('.badge-number-2').textContent = cartCount;
                }
            };
            xhr.send();
        }

        updateCartCount();

        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const alertContainer = document.getElementById('alertContainer');
                        const successMessage = document.createElement('div');
                        successMessage.className = 'alert alert-success alert-dismissible fade show';
                        successMessage.role = 'alert';
                        successMessage.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                        alertContainer.appendChild(successMessage);

                        // Update cart count after success
                        updateCartCount();

                        // Reset the form
                        addToCartForm.reset();
                        productImage.src = '../uploads/<?php echo $product['variants'][0]['product_image']; ?>';
                        priceDisplay.textContent = `$<?php echo $product['variants'][0]['price']; ?>`;
                        variantIdInput.value = '';
                        priceInput.value = '';
                        quantityInput.max = '1';
                        quantityInput.value = '1';
                        outOfStockMessage.style.display = 'none';
                        addToCartButton.style.display = 'block';
                    } else {
                        if (data.modal) {
                            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                            loginModal.show();
                        } else {
                            alert(data.message);
                        }
                    }
                })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
<?php
include 'footer.php';
?>
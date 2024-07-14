<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'navbar.php';
ob_start();

// Check if a product_id is provided in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo "Product ID is required.";
    exit;
}

// Move session start and header function before any output
$product_id = intval($_GET['product_id']);

// Fetch product details including quantity
$sql_product = "SELECT p.product_id, p.product_name, p.description, p.category_id, pv.price, pv.product_image, c.color_name, c.color_code, s.size_name, pv.quantity 
                FROM products p
                LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                LEFT JOIN colors c ON pv.color_id = c.color_id
                LEFT JOIN sizes s ON pv.size_id = s.size_id
                WHERE p.product_id = $product_id";
$result_product = $conn->query($sql_product);

if ($result_product === false || $result_product->num_rows === 0) {
    echo "Product not found.";
    exit;
}

$product = $result_product->fetch_assoc();

// Fetch all colors and sizes for the product
$sql_colors = "SELECT DISTINCT c.color_id, c.color_name, c.color_code, pv.product_image as color_image
               FROM product_variants pv
               LEFT JOIN colors c ON pv.color_id = c.color_id
               WHERE pv.product_id = $product_id";
$result_colors = $conn->query($sql_colors);

$colors = [];
if ($result_colors->num_rows > 0) {
    while ($row = $result_colors->fetch_assoc()) {
        if ($row['color_name'] !== 'None') {
            $colors[] = $row;
        }
    }
}

$sql_sizes = "SELECT DISTINCT s.size_id, s.size_name
              FROM product_variants pv
              LEFT JOIN sizes s ON pv.size_id = s.size_id
              WHERE pv.product_id = $product_id";
$result_sizes = $conn->query($sql_sizes);

$sizes = [];
if ($result_sizes->num_rows > 0) {
    while ($row = $result_sizes->fetch_assoc()) {
        $sizes[] = $row;
    }
}

// Fetch category name
$category_name = '';
if (!empty($product['category_id'])) {
    $sql_category = "SELECT category_name FROM categories WHERE category_id = " . $product['category_id'];
    $result_category = $conn->query($sql_category);

    if ($result_category && $result_category->num_rows > 0) {
        $category = $result_category->fetch_assoc();
        $category_name = $category['category_name'];
    }
}

// Fetch similar products based on category
$similarProducts = [];
if (!empty($product['category_id'])) {
    $sql_similar_products = "SELECT DISTINCT p.product_id, p.product_name, p.category_id, pv.price, pv.product_image
                            FROM products p
                            LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                            WHERE p.category_id = {$product['category_id']} AND p.product_id != $product_id
                            GROUP BY p.product_id
                            LIMIT 6";

    $result_similar_products = $conn->query($sql_similar_products);

    if ($result_similar_products && $result_similar_products->num_rows > 0) {
        while ($row = $result_similar_products->fetch_assoc()) {
            $similarProducts[] = $row;
        }
    }
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $variant_id = $_POST['variant_id'];
        $quantity = intval($_POST['quantity']);

        // Check if product variant is already in cart
        $sql_check_cart = "SELECT * FROM cart WHERE user_id = $user_id AND variant_id = $variant_id";
        $result_check_cart = $conn->query($sql_check_cart);

        if ($result_check_cart === false) {
            echo "Error checking cart: " . mysqli_error($conn);
        } elseif ($result_check_cart->num_rows > 0) {
            // Update quantity in cart
            $sql_update_cart = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND variant_id = $variant_id";
            $result_update_cart = $conn->query($sql_update_cart);
            if ($result_update_cart === false) {
                echo "Error updating cart: " . mysqli_error($conn);
            }
        } else {
            // Insert new item into cart
            $sql_add_to_cart = "INSERT INTO cart (user_id, product_id, variant_id, quantity) VALUES ($user_id, $product_id, $variant_id, $quantity)";
            $result_add_to_cart = $conn->query($sql_add_to_cart);
            if ($result_add_to_cart === false) {
                echo "Error adding to cart: " . mysqli_error($conn);
            }
        }
        echo '<script>window.location.href = "cart.php";</script>';
        exit;
    } else {
        // Redirect to login page if user is not logged in
        echo '<script>window.location.href = "usr_login.php";</script>';
        exit;
    }
}
?>

<main class="container-fluid my-5">
    <div class="container-lg container-fluid">
        <div class="row">
            <div class="col-md-6 text-center mb-3 mb-md-0">
                <img src="../uploads/<?php echo $product['product_image']; ?>" class="img-fluid fixed-height" alt="<?php echo $product['product_name']; ?>" id="mainProductImage">
            </div>
            <div class="col-md-6">
                <h2 class="pd-details-h2 fw-bold mb-0 text-sm-center text-lg-start text-md-start text-xs-center"><?php echo $product['product_name']; ?></h2>
                <?php if (!empty($category_name)) : ?>
                    <small class="text-muted d-block text-sm-center text-lg-start text-md-start text-xs-center">Category: <span class="text-primary"><?php echo $category_name; ?></span></small>
                <?php endif; ?>
                <p class="pd-details-price fw-bold fs-3 mt-2 text-sm-center text-lg-start text-md-start text-xs-center">$<?php echo $product['price']; ?></p>

                <form method="POST" action="">
                    <?php if (!empty($colors)) : ?>
                        <div class="my-3">
                            <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Colors</h5>
                            <div class="text-sm-center text-lg-start text-md-start text-xs-center">
                                <?php foreach ($colors as $color) : ?>
                                    <div class="d-inline-block me-2">
                                        <?php if ($color['color_name'] === 'Multicolor') : ?>
                                            <img src="../assets/img/multicolor.png" class="rounded-circle pd-color-circle-details align-baseline" data-variant-image="../uploads/<?php echo $color['color_image']; ?>" alt="Multicolor" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Multicolor" data-variant-id="<?php echo $color['color_id']; ?>">
                                        <?php else : ?>
                                            <div class="rounded-circle pd-color-circle-details" style="background-color: <?php echo $color['color_code']; ?>;" data-variant-image="../uploads/<?php echo $color['color_image']; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php echo $color['color_name']; ?>" data-variant-id="<?php echo $color['color_id']; ?>"></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <span id="colorValidation" class="text-danger d-none mt-2">Please select a color.</span>
                        </div>
                    <?php endif; ?>

                    <?php if (count($sizes) > 0) : ?>
                        <div class="my-3">
                            <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Sizes</h5>
                            <div class="text-sm-center text-lg-start text-md-start text-xs-center">
                                <?php foreach ($sizes as $index => $size) : ?>
                                    <label>
                                        <input type="radio" name="size" value="<?php echo $size['size_id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>> <?php echo $size['size_name']; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <span id="sizeValidation" class="text-danger d-none mt-2">Please select a size.</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($product['quantity'] > 0) : ?>
                        <div class="my-3">
                            <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Quantity</h5>
                            <div class="input-group input-md-center">
                                <button class="btn btn-outline-primary rounded-0" type="button" id="quantityMinus">-</button>
                                <input type="text" class="form-control text-center quantity-input" value="1" id="quantityInput" readonly>
                                <button class="btn btn-outline-primary rounded-0" type="button" id="quantityPlus">+</button>
                            </div>
                            <span id="quantityValidation" class="text-danger d-none mt-2">Maximum quantity reached</span>
                        </div>
                    <?php else : ?>
                        <div class="my-3">
                            <p class="text-danger fw-bold text-sm-center text-lg-start text-md-start text-xs-center">Out of Stock</p>
                        </div>
                    <?php endif; ?>

                    <input type="hidden" name="variant_id" id="variantIdInput" value="">
                    <input type="hidden" name="quantity" id="quantityHiddenInput" value="1">

                    <div class="text-sm-center text-lg-start text-md-start text-xs-center">
                        <button type="submit" class="btn btn-primary rounded-0 pd-details-btn" id="addToCartBtn">Add to Cart</button>
                    </div>
                </form>

                <p class="mt-4 pd-details-desc text-sm-center text-lg-start text-md-start text-xs-center"><?php echo $product['description']; ?></p>
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
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-primary leftLst"><i class="ri-arrow-left-s-line fw-bold h3"></i></button>
                        <button class="btn btn-primary rightLst"><i class="ri-arrow-right-s-line fw-bold h3"></i></button>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <p> No Similar Products Found. </p>
        <?php endif; ?>
    </div>
</main>

<?php
include 'footer.php';
ob_end_flush(); // Flush the output buffer
?>

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    // Tooltip Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Quantity Buttons
    document.getElementById('quantityPlus').addEventListener('click', function() {
        var quantityInput = document.getElementById('quantityInput');
        var currentValue = parseInt(quantityInput.value);
        var maxQuantity = Math.min(10, <?php echo $product['quantity']; ?>);

        if (!isNaN(currentValue) && currentValue < maxQuantity) {
            quantityInput.value = currentValue + 1;
            document.getElementById('quantityHiddenInput').value = currentValue + 1;
        } else {
            document.getElementById('quantityValidation').classList.remove('d-none');
            document.getElementById('quantityValidation').innerText = 'Maximum quantity reached';
        }
    });

    document.getElementById('quantityMinus').addEventListener('click', function() {
        var quantityInput = document.getElementById('quantityInput');
        var currentValue = parseInt(quantityInput.value);
        var maxQuantity = Math.min(10, <?php echo $product['quantity']; ?>);

        if (!isNaN(currentValue) && currentValue > 1) {
            quantityInput.value = currentValue - 1;
            document.getElementById('quantityHiddenInput').value = currentValue - 1;
            document.getElementById('quantityValidation').classList.add('d-none');
        }
    });

    // Image Swap for Color Variants
    document.querySelectorAll('.pd-color-circle-details').forEach(function(element) {
        element.addEventListener('click', function() {
            var variantId = this.getAttribute('data-variant-id');
            var variantImage = this.getAttribute('data-variant-image');

            document.getElementById('mainProductImage').src = variantImage;
            document.getElementById('variantIdInput').value = variantId;

            document.querySelectorAll('.pd-color-circle-details').forEach(function(el) {
                el.classList.remove('selected-color');
            });

            this.classList.add('selected-color');
            document.getElementById('colorValidation').classList.add('d-none');
        });
    });

    // Form Validation
    document.getElementById('addToCartBtn').addEventListener('click', function(event) {
        var variantId = document.getElementById('variantIdInput').value;
        var sizeRadioButtons = document.querySelectorAll('input[name="size"]');
        var sizeSelected = false;

        sizeRadioButtons.forEach(function(radioButton) {
            if (radioButton.checked) {
                sizeSelected = true;
            }
        });

        console.log('Variant ID:', variantId);
        console.log('Size Selected:', sizeSelected);

        if (!variantId || !sizeSelected) {
            event.preventDefault();

            if (!variantId) {
                var colorValidation = document.getElementById('colorValidation');
                if (colorValidation) {
                    colorValidation.classList.remove('d-none');
                }
            }

            if (!sizeSelected) {
                var sizeValidation = document.getElementById('sizeValidation');
                if (sizeValidation) {
                    sizeValidation.classList.remove('d-none');
                }
            }
        }
    });
</script>
<?php
session_start();
include 'navbar.php';

// Check if a product_id is provided in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo "Product ID is required.";
    exit;
}

$product_id = intval($_GET['product_id']);

// Fetch product details
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
    $variants[$row['color_id']][$row['size_id']] = $row['variant_id'];
}

// Fetch similar products based on category
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
            <!-- Move the alert box container above the product image -->
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
                    <div class="form-group center-md-pd-details">
                        <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center">Quantity</h5>
                        <div class="input-group mb-3 center-md-pd-details">
                            <button type="button" class="btn btn-outline-primary rounded-0" id="decreaseQuantity"><i class="ri-subtract-line fw-bold text-white icon-hover"></i></button>
                            <input type="number" class="form-control text-center quantity-input" id="quantity" name="quantity" value="1" min="1" readonly>
                            <button type="button" class="btn btn-outline-primary rounded-0" id="increaseQuantity"><i class="ri-add-line fw-bold text-white"></i></button>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="variant_id" id="variantId" value="">
                    <input type="hidden" name="price" id="price" value="">
                    <div class="center-md-pd-details">
                        <button type="submit" class="btn btn-primary rounded-0">Add to Cart</button>
                    </div>
                </form>
                <h5 class="pd-details-h5 fw-bolder text-sm-center text-lg-start text-md-start text-xs-center mt-4">Description</h5>
                <p class="mt-0 pd-details-desc text-sm-center text-lg-start text-md-start text-xs-center"><?php echo $product['description']; ?></p>
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

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        var variants = <?php echo json_encode($variants); ?>;
        var sizes = <?php echo json_encode($sizes); ?>;

        $('#colorSelect').change(function() {
            var colorId = $(this).val();
            var image = $(this).find(':selected').data('image');
            var price = $(this).find(':selected').data('price');

            $('#productImage').attr('src', image);
            $('#productPrice').text('Price: ' + price);
            $('#price').val(price);
            $('#variantId').val('');

            var sizeSelect = $('#sizeSelect');
            sizeSelect.empty();
            sizeSelect.append('<option value="">Select Size</option>');

            if (colorId) {
                $.each(variants[colorId], function(sizeId, variantId) {
                    var sizeName = sizes[sizeId];
                    sizeSelect.append('<option value="' + sizeId + '" data-variant-id="' + variantId + '">' + sizeName + '</option>');
                });
            }
        });

        $('#sizeSelect').change(function() {
            var variantId = $(this).find(':selected').data('variant-id');
            $('#variantId').val(variantId);
        });

        $('#addToCartForm').submit(function(e) {
            e.preventDefault();

            if ($('#variantId').val() === '') {
                alert('Please select a valid color and size combination.');
                return;
            }

            // Check if the user is logged in
            <?php if (!isset($_SESSION['user_id'])) : ?>
                window.location.href = 'usr_login.php';
                return;
            <?php endif; ?>

            $.ajax({
                url: 'add_to_cart.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Set session success message
                    <?php $_SESSION['success_message'] = "Product added to cart successfully."; ?>

                    // Display success message above the product image
                    let successMessage = "<?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : ''; ?>";
                    if (successMessage !== "") {
                        let alertBox = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            successMessage +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        $('#alertContainer').html(alertBox);
                    }

                    // Redirect to refresh the page with updated message
                    window.location.href = 'product_details.php?product_id=<?php echo $product_id; ?>';
                },
                error: function() {
                    alert('Failed to add product to cart.');
                }
            });
        });

        $('#decreaseQuantity').click(function() {
            var quantity = parseInt($('#quantity').val());
            if (quantity > 1) {
                $('#quantity').val(quantity - 1);
            }
        });

        $('#increaseQuantity').click(function() {
            var quantity = parseInt($('#quantity').val());
            $('#quantity').val(quantity + 1);
        });

        // Clear session message after displaying
        <?php unset($_SESSION['success_message']); ?>
    });
</script>

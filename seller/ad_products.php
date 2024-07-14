<?php
session_start();
include '../database/conn.php';

// Initialize variables to hold form data and errors
$productName = $productDesc = $productCategory = $productLength = $productWidth = $productHeight = $productWeight = '';
$productColors = $productSizes = $productPrices = $productQuantities = [];

$errors = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and store basic product details
    $productName = $_POST['productName'];
    $productDesc = $_POST['productDesc'];
    $productCategory = $_POST['productCategory'];
    $productLength = $_POST['productLength'];
    $productWidth = $_POST['productWidth'];
    $productHeight = $_POST['productHeight'];
    $productWeight = $_POST['productWeight'];

    // Validate each field
    if (empty($productName)) {
        $errors['productName'] = 'Product Name is required';
    } elseif (strlen($productName) > 150) {
        $errors['productName'] = 'Product Name should not exceed 150 characters';
    }

    if (empty($productDesc)) {
        $errors['productDesc'] = 'Product Description is required';
    } elseif (strlen($productDesc) > 1000) {
        $errors['productDesc'] = 'Product Description should not exceed 1000 characters';
    }

    if (empty($productCategory)) {
        $errors['productCategory'] = 'Product Category is required';
    }

    if (empty($productCategory)) {
        $errors['productCategory'] = 'Product Category is required';
    }

    if (empty($productLength)) {
        $errors['productLength'] = 'Product Length is required';
    }

    if (empty($productWidth)) {
        $errors['productWidth'] = 'Product Width is required';
    }

    if (empty($productHeight)) {
        $errors['productHeight'] = 'Product Height is required';
    }

    if (empty($productWeight)) {
        $errors['productWeight'] = 'Product Weight is required';
    }

    // Process product variants (multiple rows)
    $productColors = $_POST['productColor'];
    $productSizes = $_POST['productSize'];
    $productPrices = $_POST['productPrice'];
    $productQuantities = $_POST['productQuantity'];
    $productImages = $_FILES['productImage'];

    // Validate variant fields
    foreach ($productColors as $key => $color) {
        if (empty($color)) {
            $errors['productColor'][$key] = 'Color is required for variant ' . ($key + 1);
        }
    }

    foreach ($productSizes as $key => $size) {
        if (empty($size)) {
            $errors['productSize'][$key] = 'Size is required for variant ' . ($key + 1);
        }
    }

    foreach ($productPrices as $key => $price) {
        if (empty($price)) {
            $errors['productPrice'][$key] = 'Price is required for variant ' . ($key + 1);
        } elseif (!is_numeric($price) || $price < 10 || $price > 1000000) {
            $errors['productPrice'][$key] = 'Price must be between 10 and 1,000,000';
        }
    }

    foreach ($productQuantities as $key => $quantity) {
        if (empty($quantity)) {
            $errors['productQuantity'][$key] = 'Quantity is required for variant ' . ($key + 1);
        } elseif (!ctype_digit((string) $quantity) || $quantity < 0 || $quantity > 9999) {
            $errors['productQuantity'][$key] = 'Quantity must be between 0 and 9,999';
        }
    }    

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        // Store product details into `products` table
        $insertProductSQL = "INSERT INTO products (seller_id, category_id, product_name, description, length, width, height, weight, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($insertProductSQL);
        $stmt->bind_param("iissdddd", $_SESSION['seller_id'], $productCategory, $productName, $productDesc, $productLength, $productWidth, $productHeight, $productWeight);
        $stmt->execute();
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Store each product variant into `product_variants` table
        $insertVariantSQL = "INSERT INTO product_variants (product_id, color_id, size_id, quantity, price, product_image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertVariantSQL);

        for ($i = 0; $i < count($productColors); $i++) {
            $productImageName = basename($productImages['name'][$i]);
            $targetFilePath = "../uploads/" . $productImageName;

            if (move_uploaded_file($productImages['tmp_name'][$i], $targetFilePath)) {
                $stmt->bind_param("iiidis", $product_id, $productColors[$i], $productSizes[$i], $productQuantities[$i], $productPrices[$i], $productImageName);
                $stmt->execute();
            }
        }

        $stmt->close();
        header("Location: slr_products.php?created=1");
        exit();
    }
}

// Fetch categories for dropdown
$sql_categories = "SELECT * FROM categories ORDER BY category_name";
$result_categories = $conn->query($sql_categories);

// Fetch colors for variant dropdowns
$sql_colors = "SELECT * FROM colors ORDER BY color_name";
$result_colors = $conn->query($sql_colors);

// Fetch sizes for variant dropdowns
$sql_sizes = "SELECT * FROM sizes ORDER BY size_name";
$result_sizes = $conn->query($sql_sizes);

// Initialize at least one variant field if none are present
if (empty($productColors)) {
    $productColors[] = '';
    $productSizes[] = '';
    $productPrices[] = '';
    $productQuantities[] = '';
}

include 'header.php';
include 'sidebar.php';
?>

<main id="main-admin" class="main-admin">

    <div class="row d-flex justify-content-center">
        <div class="col-lg-10">
            <div class="card category-card">
                <div class="card-body">
                    <h5 class="card-title text-center mt-2 fs-3">List Product</h5>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                        <div class="row mb-3">
                            <label for="productName" class="col-sm-3 col-form-label category-label">Product Name</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-box"></i></span>
                                    <input type="text" class="form-control category-input" name="productName" id="productName" value="<?php echo htmlspecialchars($productName); ?>">
                                </div>
                                <?php if (isset($errors['productName'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productName']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productDesc" class="col-sm-3 col-form-label category-label">Description</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-detail"></i></span>
                                    <textarea class="form-control category-input" name="productDesc" id="productDesc" rows="3"><?php echo htmlspecialchars($productDesc); ?></textarea>
                                </div>
                                <?php if (isset($errors['productDesc'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productDesc']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productCategory" class="col-sm-3 col-form-label category-label">Category</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-category"></i></span>
                                    <select class="form-select category-input" name="productCategory">
                                        <option value="">Select Category</option>
                                        <?php
                                        if ($result_categories->num_rows > 0) {
                                            while ($row = $result_categories->fetch_assoc()) {
                                                $selected = ($productCategory == $row['category_id']) ? 'selected' : '';
                                                echo "<option value='" . $row['category_id'] . "' $selected>" . $row['category_name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php if (isset($errors['productCategory'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productCategory']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Variant fields container -->
                        <div id="variantFieldsContainer">
                            <?php for ($i = 0; $i < count($productColors); $i++) { ?>
                                <div class="row mb-3 variant-fields">
                                    <div class="col-sm-11 offset-sm-1 mb-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="col-form-label category-label">Color</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bx-paint"></i></span>
                                                    <select class="form-select category-input" name="productColor[]">
                                                        <option value="">Select Color</option>
                                                        <?php
                                                        $result_colors->data_seek(0);
                                                        while ($row = $result_colors->fetch_assoc()) {
                                                            $selected = (isset($productColors[$i]) && $productColors[$i] == $row['color_id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['color_id'] . '" ' . $selected . '>' . $row['color_name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php if (isset($errors['productColor'][$i])) : ?>
                                                    <div class="text-danger"><?php echo $errors['productColor'][$i]; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="col-form-label category-label">Size</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bx-ruler"></i></span>
                                                    <select class="form-select category-input" name="productSize[]">
                                                        <option value="">Select Size</option>
                                                        <?php
                                                        $result_sizes->data_seek(0);
                                                        while ($row = $result_sizes->fetch_assoc()) {
                                                            $selected = (isset($productSizes[$i]) && $productSizes[$i] == $row['size_id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['size_id'] . '" ' . $selected . '>' . $row['size_name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php if (isset($errors['productSize'][$i])) : ?>
                                                    <div class="text-danger"><?php echo $errors['productSize'][$i]; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-11 offset-sm-1 mb-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="col-form-label category-label">Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bx-dollar"></i></span>
                                                    <input type="text" class="form-control category-input" name="productPrice[]" value="<?php echo isset($productPrices[$i]) ? htmlspecialchars($productPrices[$i]) : ''; ?>">
                                                </div>
                                                <?php if (isset($errors['productPrice'][$i])) : ?>
                                                    <div class="text-danger"><?php echo $errors['productPrice'][$i]; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="col-form-label category-label">Quantity</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bx-cart"></i></span>
                                                    <input type="text" class="form-control category-input" name="productQuantity[]" value="<?php echo isset($productQuantities[$i]) ? htmlspecialchars($productQuantities[$i]) : ''; ?>">
                                                </div>
                                                <?php if (isset($errors['productQuantity'][$i])) : ?>
                                                    <div class="text-danger"><?php echo $errors['productQuantity'][$i]; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <label for="productImage[]" class="col-form-label">Image:</label>
                                                <input type="file" name="productImage[]" accept="image/*" onchange="previewImage(event, this)">
                                                <img id="imagePreview" src="" alt="Image Preview" style="display:none; width: 100px; height: 100px; margin-top: 10px;">
                                                <?php if (isset($errors['productImage'][$i])) : ?>
                                                    <div class="text-danger"><?php echo $errors['productImage'][$i]; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-danger remove-variant-btn">Remove</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Add more variant button -->
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary" id="addVariantBtn">Add Variant</button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3 col">
                                <label for="productLength" class="col-form-label category-label">Length (cm)</label>
                                <div class="input-group grp-fields">
                                    <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                    <input type="text" class="form-control " name="productLength" id="productLength" value="<?php echo htmlspecialchars($productLength); ?>">
                                </div>
                                <?php if (isset($errors['productLength'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productLength']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label for="productWidth" class="col-form-label category-label">Width (cm)</label>
                                <div class="input-group grp-fields">
                                    <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                    <input type="text" class="form-control" name="productWidth" id="productWidth" value="<?php echo htmlspecialchars($productWidth); ?>">
                                </div>
                                <?php if (isset($errors['productWidth'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productWidth']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label for="productHeight" class="col-form-label category-label">Height (cm)</label>
                                <div class="input-group grp-fields">
                                    <span class="input-group-text"><i class="bi bi-box2-fill"></i></span>
                                    <input type="text" class="form-control" name="productHeight" id="productHeight" value="<?php echo htmlspecialchars($productHeight); ?>">
                                </div>
                                <?php if (isset($errors['productHeight'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productHeight']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label for="productWeight" class="col-form-label category-label">Weight (kg)</label>
                                <div class="input-group grp-fields">
                                    <span class="input-group-text"><i class="ri-weight-fill"></i></span>
                                    <input type="text" class="form-control" name="productWeight" id="productWeight" value="<?php echo htmlspecialchars($productWeight); ?>">
                                </div>
                                <?php if (isset($errors['productWeight'])) : ?>
                                    <div class="text-danger"><?php echo $errors['productWeight']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-5"></div>
                            <div class="col-sm-7">
                                <button type="submit" class="btn btn-primary">Add Item</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- JavaScript for dynamic variant fields -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addVariantBtn = document.getElementById('addVariantBtn');
        const variantFieldsContainer = document.getElementById('variantFieldsContainer');

        addVariantBtn.addEventListener('click', function() {
            // Use a loop in JavaScript to dynamically create the options for colors and sizes
            const variantFields = `
            <div class="row mb-3 variant-fields">
                <div class="col-sm-11 offset-sm-1 mb-2">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="col-form-label category-label">Color</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-paint"></i></span>
                                <select class="form-select category-input" name="productColor[]">
                                    <option value="">Select Color</option>
                                    <?php
                                    $result_colors->data_seek(0);
                                    while ($row = $result_colors->fetch_assoc()) {
                                        echo '<option value="' . $row['color_id'] . '">' . $row['color_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label category-label">Size</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-ruler"></i></span>
                                <select class="form-select category-input" name="productSize[]">
                                    <option value="">Select Size</option>
                                    <?php
                                    $result_sizes->data_seek(0);
                                    while ($row = $result_sizes->fetch_assoc()) {
                                        echo '<option value="' . $row['size_id'] . '">' . $row['size_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-11 offset-sm-1 mb-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="col-form-label category-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-dollar"></i></span>
                                <input type="text" class="form-control category-input" name="productPrice[]">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label category-label">Quantity</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-cart"></i></span>
                                <input type="text" class="form-control category-input" name="productQuantity[]">
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="productImage[]" class="col-form-label">Image:</label>
                            <input type="file" name="productImage[]" accept="image/*" onchange="previewImage(event, this)">
                            <img id="imagePreview" src="" alt="Image Preview" style="display:none; width: 100px; height: 100px; margin-top: 10px;">
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-danger remove-variant-btn">Remove</button>
                </div>
            </div>
        `;
            variantFieldsContainer.insertAdjacentHTML('beforeend', variantFields);

            // Update remove button visibility after adding a variant
            updateRemoveButtonVisibility();
        });

        // Function to update remove button visibility based on number of variants
        function updateRemoveButtonVisibility() {
            const variants = document.querySelectorAll('.variant-fields');
            variants.forEach((variant, index) => {
                const removeButton = variant.querySelector('.remove-variant-btn');
                if (index === 0) {
                    removeButton.style.display = 'none'; // Hide remove button for the first variant
                } else {
                    removeButton.style.display = 'block'; // Show remove button for subsequent variants
                }
            });
        }

        // Initial update of remove button visibility
        updateRemoveButtonVisibility();

        // Handle dynamic removal of variant fields
        variantFieldsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant-btn')) {
                e.target.closest('.variant-fields').remove();
                updateRemoveButtonVisibility();
            }
        });
    });

    function previewImage(event, input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = input.nextElementSibling;
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<?php include 'footer.php'; ?>
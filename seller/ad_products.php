<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Initialize variables to hold form data
$productName = $productDesc = $productCategory = $productLength = $productWidth = $productHeight = $productWeight = '';
$productColor = $productSize = $productPrice = $productQuantity = [];

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

    // Store product details into `products` table
    $insertProductSQL = "INSERT INTO products (seller_id, category_id, product_name, description, length, width, height, weight, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($insertProductSQL);
    $stmt->bind_param("iisddddd", $_SESSION['seller_id'], $productCategory, $productName, $productDesc, $productLength, $productWidth, $productHeight, $productWeight);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Process product variants (multiple rows)
    $productColors = $_POST['productColor'];
    $productSizes = $_POST['productSize'];
    $productPrices = $_POST['productPrice'];
    $productQuantities = $_POST['productQuantity'];

    // Store each product variant into `product_variants` table
    $insertVariantSQL = "INSERT INTO product_variants (product_id, color_id, size_id, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertVariantSQL);

    for ($i = 0; $i < count($productColors); $i++) {
        $stmt->bind_param("iiidi", $product_id, $productColors[$i], $productSizes[$i], $productQuantities[$i], $productPrices[$i]);
        $stmt->execute();
    }

    $stmt->close();


    // echo "<script>window.location.href = 'dashboard.php';</script>";
    header("Location: dashboard.php");
    exit();
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
?>

<main id="main-admin" class="main-admin">

    <div class="row d-flex justify-content-center">
        <div class="col-lg-8">
            <div class="card category-card">
                <div class="card-body">
                    <h5 class="card-title text-center fs-3">Create Product</h5>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                        <div class="row mb-3">
                            <label for="productName" class="col-sm-3 col-form-label category-label">Product Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control category-input" name="productName" id="productName" value="<?php echo htmlspecialchars($productName); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productDesc" class="col-sm-3 col-form-label category-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control category-input" name="productDesc" id="productDesc" rows="3"><?php echo htmlspecialchars($productDesc); ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productCategory" class="col-sm-3 col-form-label category-label">Category</label>
                            <div class="col-sm-9">
                                <select class="form-select category-input" name="productCategory" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $sql_categories = "SELECT * FROM categories ORDER BY category_name";
                                    $result_categories = $conn->query($sql_categories);
                                    if ($result_categories->num_rows > 0) {
                                        while ($row = $result_categories->fetch_assoc()) {
                                            $selected = ($productCategory == $row['category_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['category_id'] . "' $selected>" . $row['category_name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Variant fields container -->
                        <div id="variantFieldsContainer">
                            <div class="row mb-3 variant-fields">
                                <div class="col-sm-10 offset-sm-2 mb-2">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label class="col-form-label category-label">Color</label>
                                            <select class="form-select category-input" name="productColor[]" required>
                                                <option value="">Select Color</option>
                                                <?php
                                                $sql_colors = "SELECT * FROM colors ORDER BY color_name";
                                                $result_colors = $conn->query($sql_colors);
                                                if ($result_colors->num_rows > 0) {
                                                    while ($row = $result_colors->fetch_assoc()) {
                                                        echo "<option value='" . $row['color_id'] . "'>" . $row['color_name'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="col-form-label category-label">Size</label>
                                            <select class="form-select category-input" name="productSize[]" required>
                                                <option value="">Select Size</option>
                                                <?php
                                                $sql_sizes = "SELECT * FROM sizes ORDER BY size_name";
                                                $result_sizes = $conn->query($sql_sizes);
                                                if ($result_sizes->num_rows > 0) {
                                                    while ($row = $result_sizes->fetch_assoc()) {
                                                        echo "<option value='" . $row['size_id'] . "'>" . $row['size_name'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-10 offset-sm-2">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label class="col-form-label category-label">Price</label>
                                            <input type="text" class="form-control category-input" name="productPrice[]" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="col-form-label category-label">Quantity</label>
                                            <input type="text" class="form-control category-input" name="productQuantity[]" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add more variant button -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-secondary" id="addVariantBtn">Add Variant</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productLength" class="col-sm-5 col-form-label category-label">Length (cm)</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control category-input" name="productLength" id="productLength" value="<?php echo htmlspecialchars($productLength); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productWidth" class="col-sm-5 col-form-label category-label">Width (cm)</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control category-input" name="productWidth" id="productWidth" value="<?php echo htmlspecialchars($productWidth); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productHeight" class="col-sm-5 col-form-label category-label">Height (cm)</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control category-input" name="productHeight" id="productHeight" value="<?php echo htmlspecialchars($productHeight); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productWeight" class="col-sm-5 col-form-label category-label">Weight (kg)</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control category-input" name="productWeight" id="productWeight" value="<?php echo htmlspecialchars($productWeight); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-5"></div>
                            <div class="col-sm-7">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
        // Handle dynamic addition of variant fields
        const addVariantBtn = document.getElementById('addVariantBtn');
        const variantFieldsContainer = document.getElementById('variantFieldsContainer');

        addVariantBtn.addEventListener('click', function() {
            const variantFields = `
                <div class="row mb-3 variant-fields">
                    <div class="col-sm-10 offset-sm-2 mb-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="col-form-label category-label">Color</label>
                                <select class="form-select category-input" name="productColor[]" required>
                                    <option value="">Select Color</option>
                                    <?php
                                    $result_colors->data_seek(0); // Reset the result set pointer
                                    while ($row = $result_colors->fetch_assoc()) {
                                        echo "<option value='" . $row['color_id'] . "'>" . $row['color_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="col-form-label category-label">Size</label>
                                <select class="form-select category-input" name="productSize[]" required>
                                    <option value="">Select Size</option>
                                    <?php
                                    $result_sizes->data_seek(0); // Reset the result set pointer
                                    while ($row = $result_sizes->fetch_assoc()) {
                                        echo "<option value='" . $row['size_id'] . "'>" . $row['size_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="col-form-label category-label">Price</label>
                                <input type="text" class="form-control category-input" name="productPrice[]" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="col-form-label category-label">Quantity</label>
                                <input type="text" class="form-control category-input" name="productQuantity[]" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-danger remove-variant-btn">Remove</button>
                    </div>
                </div>
            `;
            variantFieldsContainer.insertAdjacentHTML('beforeend', variantFields);
        });

        // Handle dynamic removal of variant fields
        variantFieldsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant-btn')) {
                e.target.closest('.variant-fields').remove();
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
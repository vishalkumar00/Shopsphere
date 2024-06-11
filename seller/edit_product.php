<?php
session_start();
include '../database/conn.php';

// Initialize variables to hold form data and errors
$productName = $productDesc = $productCategory = $productLength = $productWidth = $productHeight = $productWeight = '';
$productColor = $productSize = $productPrice = $productQuantity = $productImage = '';
$errors = [];

// Fetch product and variant details from the database based on product_id and variant_id sent via GET parameter
if (isset($_GET['id']) && isset($_GET['variant_id'])) {
    $productId = $_GET['id'];
    $variantId = $_GET['variant_id'];

    // Fetch product details
    $selectProductSQL = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($selectProductSQL);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $productName = $row['product_name'];
        $productDesc = $row['description'];
        $productCategory = $row['category_id'];
        $productLength = $row['length'];
        $productWidth = $row['width'];
        $productHeight = $row['height'];
        $productWeight = $row['weight'];
    } else {
        echo "Product not found.";
        exit();
    }

    $stmt->close();

    // Fetch variant details
    $selectVariantSQL = "SELECT pv.*, c.color_name, s.size_name FROM product_variants pv
                         INNER JOIN colors c ON pv.color_id = c.color_id
                         INNER JOIN sizes s ON pv.size_id = s.size_id
                         WHERE pv.variant_id = ?";
    $stmt = $conn->prepare($selectVariantSQL);
    $stmt->bind_param("i", $variantId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $productColor = $row['color_id'];
        $productSize = $row['size_id'];
        $productPrice = $row['price'];
        $productQuantity = $row['quantity'];
        $productImage = $row['product_image'];
    } else {
        echo "Variant not found.";
        exit();
    }

    $stmt->close();
} else {
    echo "Product ID or Variant ID not provided.";
    exit();
}

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
    } elseif (strlen($productDesc) > 400) {
        $errors['productDesc'] = 'Product Description should not exceed 400 characters';
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

    // Validate variant fields
    $productColor = $_POST['productColor'];
    $productSize = $_POST['productSize'];
    $productPrice = $_POST['productPrice'];
    $productQuantity = $_POST['productQuantity'];

    if (empty($productColor)) {
        $errors['productColor'] = 'Color is required';
    }

    if (empty($productSize)) {
        $errors['productSize'] = 'Size is required';
    }

    if (empty($productPrice)) {
        $errors['productPrice'] = 'Price is required';
    } elseif (!is_numeric($productPrice) || $productPrice < 10 || $productPrice > 1000000) {
        $errors['productPrice'] = 'Price must be between 10 and 1,000,000';
    }

    if (empty($productQuantity)) {
        $errors['productQuantity'] = 'Quantity is required';
    } elseif (!ctype_digit($productQuantity) || $productQuantity < 1 || $productQuantity > 9999) {
        $errors['productQuantity'] = 'Quantity must be between 1 and 9,999';
    }

    // If no errors, proceed with database update
    if (empty($errors)) {
        // Update product details in `products` table
        $updateProductSQL = "UPDATE products SET product_name = ?, description = ?, category_id = ?, length = ?, width = ?, height = ?, weight = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ?";
        $stmt = $conn->prepare($updateProductSQL);
        $stmt->bind_param("ssiidddi", $productName, $productDesc, $productCategory, $productLength, $productWidth, $productHeight, $productWeight, $productId);
        $stmt->execute();
        $stmt->close();

        // Update variant details in `product_variants` table
        $updateVariantSQL = "UPDATE product_variants SET color_id = ?, size_id = ?, quantity = ?, price = ?, product_image = ? WHERE variant_id = ?";
        $stmt = $conn->prepare($updateVariantSQL);

        $productImageName = $productImage; // Keep current image name by default
        if (!empty($_FILES['productImage']['name'])) {
            $productImageName = basename($_FILES['productImage']['name']);
            $targetFilePath = "../uploads/" . $productImageName;
            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFilePath)) {
                $errors['productImage'] = 'Failed to upload image';
            }
        }

        if (empty($errors['productImage'])) {
            $stmt->bind_param("iiidss", $productColor, $productSize, $productQuantity, $productPrice, $productImageName, $variantId);
            $stmt->execute();
            $stmt->close();
            header("Location: slr_products.php?updated=1");
            exit();
        }
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

include 'header.php';
include 'sidebar.php';
?>

<main id="main-admin" class="main-admin">
    <div class="row d-flex justify-content-center">
        <div class="col-lg-10">
            <div class="card category-card">
                <div class="card-body">
                    <h5 class="card-title text-center mt-2 fs-3">Edit Product</h5>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $productId . '&variant_id=' . $variantId; ?>" method="post" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <label for="productName" class="col-sm-3 col-form-label category-label">Product Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control category-input <?php echo isset($errors['productName']) ? 'is-invalid' : ''; ?>" name="productName" id="productName" value="<?php echo htmlspecialchars($productName); ?>">
                                <?php if (isset($errors['productName'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productName']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productDesc" class="col-sm-3 col-form-label category-label">Product Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control category-input <?php echo isset($errors['productDesc']) ? 'is-invalid' : ''; ?>" name="productDesc" id="productDesc" rows="3"><?php echo htmlspecialchars($productDesc); ?></textarea>
                                <?php if (isset($errors['productDesc'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productDesc']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productCategory" class="col-sm-3 col-form-label category-label">Product Category</label>
                            <div class="col-sm-9">
                                <select name="productCategory" id="productCategory" class="form-select category-select <?php echo isset($errors['productCategory']) ? 'is-invalid' : ''; ?>">
                                    <option value="">-- Select Category --</option>
                                    <?php while ($category = $result_categories->fetch_assoc()) : ?>
                                        <option value="<?php echo $category['category_id']; ?>" <?php echo ($productCategory == $category['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (isset($errors['productCategory'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productCategory']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productLength" class="col-sm-3 col-form-label category-label">Product Length (cm)</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control category-input <?php echo isset($errors['productLength']) ? 'is-invalid' : ''; ?>" name="productLength" id="productLength" value="<?php echo htmlspecialchars($productLength); ?>">
                                <?php if (isset($errors['productLength'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productLength']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productWidth" class="col-sm-3 col-form-label category-label">Product Width (cm)</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control category-input <?php echo isset($errors['productWidth']) ? 'is-invalid' : ''; ?>" name="productWidth" id="productWidth" value="<?php echo htmlspecialchars($productWidth); ?>">
                                <?php if (isset($errors['productWidth'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productWidth']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productHeight" class="col-sm-3 col-form-label category-label">Product Height (cm)</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control category-input <?php echo isset($errors['productHeight']) ? 'is-invalid' : ''; ?>" name="productHeight" id="productHeight" value="<?php echo htmlspecialchars($productHeight); ?>">
                                <?php if (isset($errors['productHeight'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productHeight']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productWeight" class="col-sm-3 col-form-label category-label">Product Weight (kg)</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control category-input <?php echo isset($errors['productWeight']) ? 'is-invalid' : ''; ?>" name="productWeight" id="productWeight" value="<?php echo htmlspecialchars($productWeight); ?>">
                                <?php if (isset($errors['productWeight'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productWeight']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productColor" class="col-sm-3 col-form-label category-label">Color</label>
                            <div class="col-sm-9">
                                <select name="productColor" id="productColor" class="form-select category-select <?php echo isset($errors['productColor']) ? 'is-invalid' : ''; ?>">
                                    <option value="">-- Select Color --</option>
                                    <?php while ($color = $result_colors->fetch_assoc()) : ?>
                                        <option value="<?php echo $color['color_id']; ?>" <?php echo ($productColor == $color['color_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($color['color_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (isset($errors['productColor'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productColor']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productSize" class="col-sm-3 col-form-label category-label">Size</label>
                            <div class="col-sm-9">
                                <select name="productSize" id="productSize" class="form-select category-select <?php echo isset($errors['productSize']) ? 'is-invalid' : ''; ?>">
                                    <option value="">-- Select Size --</option>
                                    <?php while ($size = $result_sizes->fetch_assoc()) : ?>
                                        <option value="<?php echo $size['size_id']; ?>" <?php echo ($productSize == $size['size_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($size['size_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (isset($errors['productSize'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productSize']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productPrice" class="col-sm-3 col-form-label category-label">Price ($)</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control category-input <?php echo isset($errors['productPrice']) ? 'is-invalid' : ''; ?>" name="productPrice" id="productPrice" value="<?php echo htmlspecialchars($productPrice); ?>">
                                <?php if (isset($errors['productPrice'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productPrice']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productQuantity" class="col-sm-3 col-form-label category-label">Quantity</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control category-input <?php echo isset($errors['productQuantity']) ? 'is-invalid' : ''; ?>" name="productQuantity" id="productQuantity" value="<?php echo htmlspecialchars($productQuantity); ?>">
                                <?php if (isset($errors['productQuantity'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productQuantity']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="productImage" class="col-sm-3 col-form-label category-label">Product Image</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control category-input <?php echo isset($errors['productImage']) ? 'is-invalid' : ''; ?>" name="productImage" id="productImage">
                                <?php if ($productImage) : ?>
                                    <div class="mt-2" id="currentImagePreview">
                                        <img src="../uploads/<?php echo htmlspecialchars($productImage); ?>" alt="Current Product Image" style="max-width: 100px; height: auto;">
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($errors['productImage'])) : ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['productImage']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('productImage').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewDiv = document.getElementById('currentImagePreview');
                previewDiv.innerHTML = '<img src="' + e.target.result + '" alt="New Product Image" style="max-width: 100px; height: auto;">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php
include 'footer.php';
?>
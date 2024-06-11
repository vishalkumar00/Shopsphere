<?php
session_start();
include '../database/conn.php';

include 'header.php';
include 'sidebar.php';

// Fetch products and their variants from the database
$query = "SELECT 
            p.product_id, p.product_name, p.description, 
            p.length, p.width, p.height, p.weight,
            c.category_name, 
            v.variant_id, v.quantity, v.price, v.product_image,
            clr.color_name, 
            sz.size_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id
          LEFT JOIN product_variants v ON p.product_id = v.product_id
          LEFT JOIN colors clr ON v.color_id = clr.color_id
          LEFT JOIN sizes sz ON v.size_id = sz.size_id";
$result = $conn->query($query);
?>

<main id="main-admin" class="main-admin">
    <div class="row mb-3">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h3 class="slr-product-page-title">Products</h3>
            <a href="ad_products.php" class="btn btn-primary">List New Product</a>
        </div>
    </div>
    <?php
    // Check for new listed product success message
    if (isset($_GET['created']) && $_GET['created'] == '1') {
        echo '<div class="alert alert-success" role="alert">Product added successfully!</div>';
    }
    // Check for update success message
    if (isset($_GET['updated']) && $_GET['updated'] == '1') {
        echo '<div class="alert alert-warning" role="alert">Product updated successfully!</div>';
    }
    // Check for delete success message
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        echo '<div class="alert alert-danger" role="alert">Product deleted successfully!</div>';
    }
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Size</th>
                <th>Dimensions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['product_image'])) : ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" width="100">
                            <?php else : ?>
                                <img src="../uploads/default.png" alt="Default Image" width="50">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo "$" . htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['size_name']); ?></td>
                        <td>
                            <?php echo "<b>L: </b>" . htmlspecialchars($row['length']) . " cm<br> <b>W: </b>" . htmlspecialchars($row['width']) . " cm<br> <b>H: </b>" . htmlspecialchars($row['height']) . " cm<br> <b>Wt: </b>" . htmlspecialchars($row['weight']) . " kg"; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="edit_product.php?id=<?php echo $row['product_id']; ?>&variant_id=<?php echo $row['variant_id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                <a href="delete_product.php?id=<?php echo $row['product_id']; ?>&variant_id=<?php echo $row['variant_id']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
</main>

<?php include 'footer.php'; ?>
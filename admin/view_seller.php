<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;

if ($seller_id == 0) {
    header('Location: sellers_list.php');
    exit;
}

// Fetch seller details
$query = "
    SELECT 
        store_name,
        address,
        city,
        province,
        postal_code,
        business_email,
        contact_number,
        tax_id,
        bank_account_number,
        bank_name,
        transit_number,
        institution_number
    FROM sellers
    WHERE seller_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$seller_result = $stmt->get_result();
$seller = $seller_result->fetch_assoc();
$stmt->close();

// Fetch seller's products and variants
$query = "
    SELECT 
        p.product_id,
        p.product_name,
        clr.color_name,
        sz.size_name,
        v.variant_id,
        v.price,
        v.product_image
    FROM product_variants v
    JOIN products p ON v.product_id = p.product_id
    JOIN colors clr ON v.color_id = clr.color_id
    JOIN sizes sz ON v.size_id = sz.size_id
    WHERE p.seller_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$products_result = $stmt->get_result();
$stmt->close();
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Seller Detail</h3>
                <a href="sellers_list.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Sellers</a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Seller Information</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Store Name:</strong> <?php echo htmlspecialchars($seller['store_name']); ?></li>
                        <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($seller['address']) . ', ' . htmlspecialchars($seller['city']) . ', ' . htmlspecialchars($seller['province']) . ' ' . htmlspecialchars($seller['postal_code']); ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($seller['business_email']); ?></li>
                        <li class="list-group-item"><strong>Contact Number:</strong> <?php echo htmlspecialchars($seller['contact_number']); ?></li>
                        <li class="list-group-item"><strong>Tax ID:</strong> <?php echo htmlspecialchars($seller['tax_id']); ?></li>
                        <li class="list-group-item"><strong>Bank Details:</strong> <?php echo '<br>' . 'Account Number: ' . htmlspecialchars($seller['bank_account_number']) . '<br>' . 'Bank Name: ' . htmlspecialchars($seller['bank_name']) . '<br>' . 'Transit Number: ' . htmlspecialchars($seller['transit_number']) . '<br>' . 'Institution Number: ' . htmlspecialchars($seller['institution_number']); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Products and Variants</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Product ID</th>
                                    <th scope="col">Product Image</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Color</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Variant ID</th>
                                    <th scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($products_result->num_rows > 0) : ?>
                                    <?php while ($row = $products_result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                                            <td><img src="../uploads/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" width="50"></td>
                                            <td><a href="http://localhost/shopsphere/user/product_details.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>" target="_blank" class="text-primary fw-bold"><?php echo htmlspecialchars($row['product_name']); ?></a></td>
                                            <td><?php echo htmlspecialchars($row['color_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['size_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['variant_id']); ?></td>
                                            <td><b>$<?php echo number_format($row['price'], 2); ?></b></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6">No products found for this seller.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
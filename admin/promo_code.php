<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promo_name = $_POST['promo_name'];
    $discount_percent = $_POST['discount_percent'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO promo_code (promo_name, discount_percent, status) VALUES (?, ?, ?)");
    $stmt->bind_param('sds', $promo_name, $discount_percent, $status);

    if ($stmt->execute()) {
        $success_message = "Promo code created successfully.";
    } else {
        $error_message = "Error creating promo code: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch existing promo codes
$promo_codes = [];
$result = $conn->query("SELECT promo_id, promo_name, discount_percent, status FROM promo_code ORDER BY promo_id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $promo_codes[] = $row;
    }
}

$conn->close();
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Promo Codes</h3>
            </div>
        </div>

        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6">
                <div class="card category-card">
                    <div class="card-body">
                        <h5 class="card-title category-card-title">Make Promo Code</h5>
                        <form action="promo_code.php" method="post" class="mb-5">
                            <div class="mb-3">
                                <label for="promo_name" class="form-label">Promo Code Name</label>
                                <input type="text" class="form-control" id="promo_name" name="promo_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="discount_percent" class="form-label">Discount Percent (%)</label>
                                <input type="number" step="0.01" class="form-control" id="discount_percent" name="discount_percent" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Promo Code</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card category-card">
                    <div class="card-body">
                        <h5 class="card-title category-card-title">Existing Promo Codes</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Promo Code Name</th>
                                    <th>Discount Percent</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($promo_codes)) : ?>
                                    <?php foreach ($promo_codes as $promo_code) : ?>
                                        <tr>
                                            <td><?php echo $promo_code['promo_name']; ?></td>
                                            <td><?php echo $promo_code['discount_percent']; ?>%</td>
                                            <td><?php echo $promo_code['status']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No promo codes found.</td>
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
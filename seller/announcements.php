<?php
include 'header.php';
include 'sidebar.php';

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Announcements</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="announcement mt-4">
                                <h5 class="fw-bold text-secondary"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p><?php echo htmlspecialchars($row['message']); ?></p>
                                <p class="text-muted"><?php echo htmlspecialchars($row['created_at']); ?></p>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>No announcements found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
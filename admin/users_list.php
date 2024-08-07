<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

$query = "SELECT 
                user_id, 
                CONCAT(first_name, ' ', last_name) AS full_name, 
                email, 
                mobile_number, 
                created_at 
          FROM users";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Customers (Users)</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Registered Customers</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Contact Number</th>
                                    <th scope="col">Registered At</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) : ?>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($row['full_name']); ?></b></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td><a href="view_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-primary"><i class="ri-file-user-fill fs-5"></i></a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">No customers found.</td>
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
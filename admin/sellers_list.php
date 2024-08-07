<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

$query = "SELECT 
                seller_id, 
                store_name, 
                CONCAT(address, ', ', city, ', ', province, ', ', postal_code) AS full_address, 
                business_email, 
                contact_number, 
                tax_id, 
                bank_account_number,
                bank_name, 
                transit_number, 
                institution_number, 
                created_at 
          FROM sellers";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Sellers</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Registered Sellers</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Store Name</th>
                                    <th scope="col">Full Address</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Contact Number</th>
                                    <th scope="col">Tax ID</th>
                                    <th scope="col">Bank Details</th>
                                    <th scope="col">Registered At</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) : ?>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($row['store_name']); ?></b></td>
                                            <td><?php echo htmlspecialchars($row['full_address']); ?></td>
                                            <td><?php echo htmlspecialchars($row['business_email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['tax_id']); ?></td>
                                            <td><b>Bank A/C No: </b><?php echo htmlspecialchars($row['bank_account_number']); ?><br><b>Bank Name: </b><?php echo htmlspecialchars($row['bank_name']); ?><br><b>Transit No: </b><?php echo htmlspecialchars($row['transit_number']); ?><br><b>Institution Number: </b><?php echo htmlspecialchars($row['institution_number']); ?><br></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td><a href="view_seller.php?seller_id=<?php echo htmlspecialchars($row['seller_id']); ?>" class="btn btn-primary"><i class="ri-file-user-fill fs-5"></i></a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7">No sellers found.</td>
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

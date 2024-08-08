<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

// Fetch seller information if not a form submission
if ($_SERVER["REQUEST_METHOD"] != "POST" && isset($_SESSION['seller_id'])) {
    $sellerId = $_SESSION['seller_id'];

    // Fetch seller information
    $query = "SELECT * FROM sellers WHERE seller_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $seller = $result->fetch_assoc();
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        die("An error occurred while fetching seller information. Please try again later.");
    }

    // Set form variables from database if not a POST request
    $address = $seller['address'];
    $city = $seller['city'];
    $province = $seller['province'];
    $postalCode = $seller['postal_code'];
    $contactNumber = $seller['contact_number'];
}

// Define variables to store form data and validation errors
$address = $city = $province = $postalCode = $contactNumber = "";
$addressErr = $cityErr = $provinceErr = $postalCodeErr = $contactNumberErr = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Validate and sanitize form inputs
    $address = htmlspecialchars(trim($_POST["address"] ?? ""));
    $city = htmlspecialchars(trim($_POST["city"] ?? ""));
    $province = htmlspecialchars(trim($_POST["province"] ?? ""));
    $postalCode = htmlspecialchars(trim($_POST["postal_code"] ?? ""));
    $contactNumber = htmlspecialchars(trim($_POST["contact_number"] ?? ""));

    if (empty($address)) {
        $addressErr = "Address is required";
    }

    if (empty($city)) {
        $cityErr = "City is required";
    }

    if (empty($province)) {
        $provinceErr = "Province is required";
    }

    if (empty($postalCode)) {
        $postalCodeErr = "Postal code is required";
    } elseif (!preg_match("/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/", $postalCode)) {
        $postalCodeErr = "Invalid postal code format";
    }

    if (empty($contactNumber)) {
        $contactNumberErr = "Contact number is required";
    } elseif (!preg_match("/^\d{10}$/", $contactNumber)) {
        $contactNumberErr = "Contact number must be a 10-digit number";
    }

    // If all fields are valid, proceed with database update
    if (empty($addressErr) && empty($cityErr) && empty($provinceErr) && empty($postalCodeErr) && empty($contactNumberErr)) {
        $sellerId = $_SESSION['seller_id'];
        $sql = "UPDATE sellers SET address = ?, city = ?, province = ?, postal_code = ?, contact_number = ? WHERE seller_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $address, $city, $province, $postalCode, $contactNumber, $sellerId);
            if ($stmt->execute()) {
                // Redirect to the profile page after update
                // header("Location: slr_profile.php");
                echo '<script>window.location.href = "slr_profile.php";</script>';
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            die("An error occurred while updating seller information. Please try again later.");
        }
    }
}

?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title">Profile</h3>
                <a href="change_password.php" class="btn btn-primary">Change Password</a>
            </div>
        </div>
        <div class="card category-card">
            <div class="card-body">
                <div class="row pt-4">
                    <div class="col-md-6">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-map'></i></span>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($seller['address'] ?? $address); ?>" required>
                                </div>
                                <span class="text-danger"><?php echo $addressErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-buildings'></i></span>
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($seller['city'] ?? $city); ?>" required>
                                </div>
                                <span class="text-danger"><?php echo $cityErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="province" class="form-label">Province</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-map-pin'></i></span>
                                    <select id="province" class="form-select" name="province" required>
                                        <option value="">Select Province</option>
                                        <option value="ON" <?php if ($province == 'ON') echo 'selected'; ?>>Ontario</option>
                                        <option value="QC" <?php if ($province == 'QC') echo 'selected'; ?>>Quebec</option>
                                        <option value="NS" <?php if ($province == 'NS') echo 'selected'; ?>>Nova Scotia</option>
                                        <option value="NB" <?php if ($province == 'NB') echo 'selected'; ?>>New Brunswick</option>
                                        <option value="MB" <?php if ($province == 'MB') echo 'selected'; ?>>Manitoba</option>
                                        <option value="BC" <?php if ($province == 'BC') echo 'selected'; ?>>British Columbia</option>
                                        <option value="PE" <?php if ($province == 'PE') echo 'selected'; ?>>Prince Edward Island</option>
                                        <option value="SK" <?php if ($province == 'SK') echo 'selected'; ?>>Saskatchewan</option>
                                        <option value="AB" <?php if ($province == 'AB') echo 'selected'; ?>>Alberta</option>
                                        <option value="NL" <?php if ($province == 'NL') echo 'selected'; ?>>Newfoundland and Labrador</option>
                                    </select>
                                </div>
                                <span class="text-danger"><?php echo $provinceErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-mail-send'></i></span>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($seller['postal_code'] ?? $postalCode); ?>" required>
                                </div>
                                <span class="text-danger"><?php echo $postalCodeErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-phone'></i></span>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($seller['contact_number'] ?? $contactNumber); ?>" required>
                                </div>
                                <span class="text-danger"><?php echo $contactNumberErr; ?></span>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" name="update_profile">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-store-alt'></i></span>
                                <input type="text" class="form-control" id="store_name" value="<?php echo htmlspecialchars($seller['store_name']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-envelope'></i></span>
                                <input type="email" class="form-control" id="business_email" value="<?php echo htmlspecialchars($seller['business_email']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tax_id" class="form-label">Tax ID</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-id-card'></i></span>
                                <input type="text" class="form-control" id="tax_id" value="<?php echo htmlspecialchars($seller['tax_id']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bank_account_number" class="form-label">Bank Account Number</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-money'></i></span>
                                <input type="text" class="form-control" id="bank_account_number" value="<?php echo htmlspecialchars($seller['bank_account_number']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class="ri-bank-line"></i></span>
                                <input type="text" class="form-control" id="bank_name" value="<?php echo htmlspecialchars($seller['bank_name']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="transit_number" class="form-label">Transit Number</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-transfer'></i></span>
                                <input type="text" class="form-control" id="transit_number" value="<?php echo htmlspecialchars($seller['transit_number']); ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="institution_number" class="form-label">Institution Number</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-credit-card'></i></span>
                                <input type="text" class="form-control" id="institution_number" value="<?php echo htmlspecialchars($seller['institution_number']); ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
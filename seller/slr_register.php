<?php
include '../database/conn.php';

// Define variables to store form data and validation errors
$storeName = $address = $city = $province = $postalCode = $businessEmail = $password = $confirmPassword = $contactNumber = $taxId = $bankAccountNumber = $bankName = $transitNumber = $institutionNumber = "";
$storeNameErr = $addressErr = $cityErr = $provinceErr = $postalCodeErr = $businessEmailErr = $passwordErr = $confirmPasswordErr = $contactNumberErr = $taxIdErr = $bankAccountNumberErr = $bankNameErr = $transitNumberErr = $institutionNumberErr = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $storeName = htmlspecialchars(trim($_POST["storeName"] ?? ""));
    $address = htmlspecialchars(trim($_POST["address"] ?? ""));
    $city = htmlspecialchars(trim($_POST["city"] ?? ""));
    $province = htmlspecialchars(trim($_POST["province"] ?? ""));
    $postalCode = htmlspecialchars(trim($_POST["postalCode"] ?? ""));
    $businessEmail = htmlspecialchars(trim($_POST["businessEmail"] ?? ""));
    $password = htmlspecialchars(trim($_POST["password"] ?? ""));
    $confirmPassword = htmlspecialchars(trim($_POST["confirmPassword"] ?? ""));
    $contactNumber = htmlspecialchars(trim($_POST["contactNumber"] ?? ""));
    $taxId = htmlspecialchars(trim($_POST["taxId"] ?? ""));
    $bankAccountNumber = htmlspecialchars(trim($_POST["bankAccountNumber"] ?? ""));
    $bankName = htmlspecialchars(trim($_POST["bankName"] ?? ""));
    $transitNumber = htmlspecialchars(trim($_POST["transitNumber"] ?? ""));
    $institutionNumber = htmlspecialchars(trim($_POST["institutionNumber"] ?? ""));

    // Validation
    if (empty($storeName)) {
        $storeNameErr = "Store name is required";
    }

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

    if (empty($businessEmail)) {
        $businessEmailErr = "Email is required";
    } elseif (!preg_match("/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/", $businessEmail)) {
        $businessEmailErr = "Invalid Email";
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    if (empty($confirmPassword)) {
        $confirmPasswordErr = "Confirm Password is required";
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordErr = "Passwords do not match";
    }

    if (empty($contactNumber)) {
        $contactNumberErr = "Contact number is required";
    } elseif (!preg_match("/^\d{10}$/", $contactNumber)) {
        $contactNumberErr = "Contact number must be a 10-digit number";
    }

    if (empty($taxId)) {
        $taxIdErr = "Tax ID is required";
    } elseif (!preg_match("/^\d{9}$/", $taxId)) {
        $taxIdErr = "Tax ID must be a 9-digit number";
    }

    if (empty($bankAccountNumber)) {
        $bankAccountNumberErr = "Bank account number is required";
    } elseif (!preg_match("/^\d{7}$/", $bankAccountNumber)) {
        $bankAccountNumberErr = "Bank account number must be a 7-digit number";
    }

    if (empty($bankName)) {
        $bankNameErr = "Bank name is required";
    }

    if (empty($transitNumber)) {
        $transitNumberErr = "Transit number is required";
    } elseif (!preg_match("/^\d{5}$/", $transitNumber)) {
        $transitNumberErr = "Transit number must be a 5-digit number";
    }

    if (empty($institutionNumber)) {
        $institutionNumberErr = "Institution number is required";
    } elseif (!preg_match("/^\d{3}$/", $institutionNumber)) {
        $institutionNumberErr = "Institution number must be a 3-digit number";
    }

    // If all fields are valid, proceed with database insertion
    if (empty($storeNameErr) && empty($addressErr) && empty($cityErr) && empty($provinceErr) && empty($postalCodeErr) && empty($businessEmailErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($contactNumberErr) && empty($taxIdErr) && empty($bankAccountNumberErr) && empty($bankNameErr) && empty($transitNumberErr) && empty($institutionNumberErr)) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute SQL INSERT statement
        $sql = "INSERT INTO sellers (store_name, address, city, province, postal_code, business_email, password, contact_number, tax_id, bank_account_number, bank_name, transit_number, institution_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssss", $storeName, $address, $city, $province, $postalCode, $businessEmail, $hashedPassword, $contactNumber, $taxId, $bankAccountNumber, $bankName, $transitNumber, $institutionNumber);
        if ($stmt->execute()) {
            // Redirect to login page 
            header("Location: index.php");
            exit();
        } else {
            // Handle database error
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Register - Seller</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="dashboard.php" class="logo-login d-flex align-items-center w-auto ">
                                    <img src="../assets/img/logo.png" alt="ShopSphere-logo">
                                </a>
                            </div>

                            <div class="card mb-3 register-form-card">

                                <div class="card-body card-register-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Create a Seller Account</h5>
                                        <p class="text-center small">Enter your business details to create an account</p>
                                    </div>

                                    <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <div class="col-md-6">
                                            <div class="col-12">
                                                <label for="storeName" class="form-label">Store Name:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-store-alt'></i></span>
                                                    <input type="text" id="storeName" class="form-control" name="storeName" value="<?php echo $storeName; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $storeNameErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="address" class="form-label">Address:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-map'></i></span>
                                                    <input type="text" id="address" class="form-control" name="address" value="<?php echo $address; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $addressErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="city" class="form-label">City:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-buildings'></i></span>
                                                    <input type="text" id="city" class="form-control" name="city" value="<?php echo $city; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $cityErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="province" class="form-label">Province:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-map-pin'></i></span>
                                                    <select id="province" class="form-select" name="province">
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

                                            <div class="col-12 mt-3">
                                                <label for="postalCode" class="form-label">Postal Code:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-mail-send'></i></span>
                                                    <input type="text" id="postalCode" class="form-control" name="postalCode" value="<?php echo $postalCode; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $postalCodeErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="contactNumber" class="form-label">Contact Number:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-phone'></i></span>
                                                    <input type="text" id="contactNumber" class="form-control" name="contactNumber" value="<?php echo $contactNumber; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $contactNumberErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="businessEmail" class="form-label">Business Email:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-envelope'></i></span>
                                                    <input type="email" id="businessEmail" class="form-control" name="businessEmail" value="<?php echo $businessEmail; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $businessEmailErr; ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-lock'></i></span>
                                                    <input type="password" id="password" class="form-control" name="password">
                                                </div>
                                                <span class="text-danger"><?php echo $passwordErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-lock'></i></span>
                                                    <input type="password" id="confirmPassword" class="form-control" name="confirmPassword">
                                                </div>
                                                <span class="text-danger"><?php echo $confirmPasswordErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="taxId" class="form-label">Tax ID:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-id-card'></i></span>
                                                    <input type="text" id="taxId" class="form-control" name="taxId" value="<?php echo $taxId; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $taxIdErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="bankAccountNumber" class="form-label">Bank Account Number:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-money'></i></span>
                                                    <input type="text" id="bankAccountNumber" class="form-control" name="bankAccountNumber" value="<?php echo $bankAccountNumber; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $bankAccountNumberErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="bankName" class="form-label">Bank Name:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class="ri-bank-line"></i></span>
                                                    <input type="text" id="bankName" class="form-control" name="bankName" value="<?php echo $bankName; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $bankNameErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="transitNumber" class="form-label">Transit Number:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-transfer'></i></span>
                                                    <input type="text" id="transitNumber" class="form-control" name="transitNumber" value="<?php echo $transitNumber; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $transitNumberErr; ?></span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="institutionNumber" class="form-label">Institution Number:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-credit-card'></i></span>
                                                    <input type="text" id="institutionNumber" class="form-control" name="institutionNumber" value="<?php echo $institutionNumber; ?>">
                                                </div>
                                                <span class="text-danger"><?php echo $institutionNumberErr; ?></span>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Create Account</button>
                                        </div>

                                        <div class="col-12 text-center">
                                            <p class="small mb-0">Already have an account? <a href="index.php">Log in</a></p>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            <div class="credits">
                                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

</body>

</html>
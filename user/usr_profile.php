<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "usr_login.php";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];
$user_info = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'mobile_number' => ''
];

$errors = []; 

// Ensure that $conn is properly initialized
if ($conn === false) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user information
$sql = "SELECT first_name, last_name, email, mobile_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_info = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile_number = trim($_POST['mobile_number']);

    // Validate phone number (must be 10 digits)
    if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    } else {
        // Update phone number if validation passes
        $stmt = $conn->prepare("UPDATE users SET mobile_number = ? WHERE user_id = ?");

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('si', $mobile_number, $user_id);
        $stmt->execute();

        // Refresh the page to show the updated phone number
        echo '<script>window.location.href = "usr_profile.php";</script>';
        exit();
    }
}
?>

<main class="container my-3">
    <div class="row mb-3">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h3 class="slr-product-page-title fw-bolder">Profile</h3>
            <a href="change_password.php" class="btn btn-primary">Change Password</a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center fw-bolder pt-4">Profile Information</h3>
                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="usr_profile.php" method="post">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" value="<?php echo htmlspecialchars($user_info['first_name']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" value="<?php echo htmlspecialchars($user_info['last_name']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">Phone Number</label>
                            <input type="text" name="mobile_number" class="form-control" id="mobile_number" value="<?php echo htmlspecialchars($user_info['mobile_number']); ?>">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Phone Number</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
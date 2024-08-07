<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "usr_login.php";</script>';
    exit();
}

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    // Validate password
    if (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation password do not match.";
    } else {
        // Fetch the current password hash
        $sql = "SELECT password FROM users WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            die("Prepare failed: " . $conn->error);
        }

        if (password_verify($current_password, $user['password'])) {
            // Update the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE user_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('si', $hashed_password, $user_id);
                $stmt->execute();
                $stmt->close();
                $success_message = "Password successfully updated.";
            } else {
                die("Prepare failed: " . $conn->error);
            }
        } else {
            $errors[] = "Current password is incorrect.";
        }
    }
}
?>

<main class="container my-3">
    <div class="row mb-3">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h3 class="slr-product-page-title fw-bolder">Change Password</h3>
            <a href="usr_profile.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Back to Profile</a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center fw-bolder pt-4">Update Password</h3>
                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success_message)) : ?>
                        <div class="alert alert-success">
                            <p><?php echo htmlspecialchars($success_message); ?></p>
                        </div>
                    <?php endif; ?>
                    <form action="change_password.php" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" id="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

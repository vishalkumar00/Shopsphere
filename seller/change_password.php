<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

$current_password = $new_password = $confirm_password = "";
$current_passwordErr = $new_passwordErr = $confirm_passwordErr = "";
$error_message = $success_message = "";

// Handle change password form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // Validate and sanitize form inputs
    $current_password = htmlspecialchars(trim($_POST["current_password"] ?? ""));
    $new_password = htmlspecialchars(trim($_POST["new_password"] ?? ""));
    $confirm_password = htmlspecialchars(trim($_POST["confirm_password"] ?? ""));

    // Fetch current seller information
    $sellerId = $_SESSION['seller_id'];
    $sql = "SELECT password FROM sellers WHERE seller_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $seller = $result->fetch_assoc();
        $stmt->close();

        // Verify current password
        if (password_verify($current_password, $seller['password'])) {
            // Validate new password
            if (empty($new_password)) {
                $new_passwordErr = "Please enter a new password";
            } elseif (strlen($new_password) < 8) {
                $new_passwordErr = "Password must be at least 8 characters long";
            } elseif ($new_password !== $confirm_password) {
                $confirm_passwordErr = "Passwords do not match";
            } else {
                // Update password in the database
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update = "UPDATE sellers SET password = ? WHERE seller_id = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("si", $hashed_password, $sellerId);
                    if ($stmt_update->execute()) {
                        $success_message = "Password updated successfully.";
                        // Clear form fields after successful update
                        $current_password = $new_password = $confirm_password = "";
                    } else {
                        $error_message = "Error updating password.";
                    }
                    $stmt_update->close();
                } else {
                    $error_message = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                }
            }
        } else {
            $current_passwordErr = "Current password is incorrect";
        }
    } else {
        $error_message = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
}

?>

<main id="main-admin" class="main-admin">
    <div class="container mt-3">
        <section class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Change Password</h3>
                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <form id="changePasswordForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="text" class="form-control" id="current_password" name="current_password" value="<?php echo htmlspecialchars($current_password); ?>" required>
                                <span class="text-danger"><?php echo $current_passwordErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="text" class="form-control" id="new_password" name="new_password" value="<?php echo htmlspecialchars($new_password); ?>" required>
                                <span class="text-danger"><?php echo $new_passwordErr; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="text" class="form-control" id="confirm_password" name="confirm_password" value="<?php echo htmlspecialchars($confirm_password); ?>" required>
                                <span class="text-danger"><?php echo $confirm_passwordErr; ?></span>
                            </div>
                            <div class="text-center mb-2">
                                <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
                                <a href="slr_profile.php" class="btn btn-secondary">Back to Profile</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>
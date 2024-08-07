<?php
session_start();
include '../database/conn.php';

$error_message = '';
$success_message = '';

$token = '';
$new_password = '';
$confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Please fill out all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $sql = "SELECT user_id, token_expiry FROM users WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (new DateTime() > new DateTime($row['token_expiry'])) {
                    $error_message = "Token has expired.";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
                    $stmt->bind_param('ss', $hashed_password, $token);
                    $stmt->execute();
                    $success_message = "Password has been reset successfully.";
                    header("Location: usr_login.php?message=" . urlencode($success_message));
                    exit();
                }
            } else {
                $error_message = "Invalid token.";
            }
        } else {
            $error_message = "Failed to prepare statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Change Password - ShopSphere</title>
<meta content="" name="description">
<meta content="" name="keywords">
<link href="../assets/img/favicon.png" rel="icon">
<link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
<link href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<main>
    <div class="container mt-5">
        <section class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Reset Password</h3>
                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <form action="reset_password.php" method="post">
                            <div class="mb-3">
                                <label for="token" class="form-label">Token</label>
                                <input type="text" name="token" class="form-control" id="token" placeholder="Enter your token" value="<?php echo htmlspecialchars($token); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Enter new password" value="<?php echo htmlspecialchars($new_password); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm new password" value="<?php echo htmlspecialchars($confirm_password); ?>">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
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

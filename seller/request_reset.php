<?php
session_start();
include '../database/conn.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if (empty($email)) {
        $error_message = "Please enter your email.";
    } else {
        $sql = "SELECT seller_id FROM sellers WHERE business_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $conn->prepare("UPDATE sellers SET reset_token = ?, token_expiry = ? WHERE business_email = ?");
            $stmt->bind_param('sss', $token, $expiry, $email);
            $stmt->execute();

            // Instead of sending an email, you can display the token for testing
            $success_message = "Use this token to reset your password: <span id='tokenText'>$token</span> <button class='btn btn-secondary btn-sm' onclick='copyToken()'><i class='bi bi-copy'></i></button>";
        } else {
            $error_message = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Request Reset Password - Seller</title>
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
    <script>
        function copyToken() {
            var tokenText = document.getElementById("tokenText").innerText;
            navigator.clipboard.writeText(tokenText).then(function() {
                alert('Token copied to clipboard');
            }, function(err) {
                alert('Failed to copy token: ', err);
            });
        }
    </script>
</head>

<body>
    <main>
        <div class="container mt-5">
            <section class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-center">Request Password Reset</h3>
                            <?php if (!empty($error_message)) : ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($success_message)) : ?>
                                <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <?php endif; ?>
                            <form action="request_reset.php" method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email">
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Request Reset</button>
                                </div>
                                <hr>
                                <div class="text-center py-1">
                                    <a href="reset_password.php" class="btn btn-success">Reset Password</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>

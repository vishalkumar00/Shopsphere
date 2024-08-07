<?php
session_start();
include '../database/conn.php';

if (isset($_SESSION['seller_id']) && isset($_SESSION['business_email'])) {
  header("Location: dashboard.php");
  exit();
}

$error_message = '';
$success_message = '';
$slrEmail = '';
$password = '';

// Check if remember me cookies are set and auto-login
if (isset($_COOKIE['seller_id']) && isset($_COOKIE['business_email']) && isset($_COOKIE['store_name'])) {
    $_SESSION['seller_id'] = $_COOKIE['seller_id'];
    $_SESSION['business_email'] = $_COOKIE['business_email'];
    $_SESSION['store_name'] = $_COOKIE['store_name'];
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slrEmail = $_POST['slrEmail'];
    $password = $_POST['password'];

    if (empty($slrEmail) || empty($password)) {
        $error_message = "Please fill out both fields.";
    } else {
        // Validate credentials against the database if fields are not empty
        $sql = "SELECT seller_id, business_email, password, store_name FROM sellers WHERE business_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $slrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            // Verify hashed password
            if (password_verify($password, $hashed_password)) {
                // Successful login
                $_SESSION['seller_id'] = $row['seller_id'];
                $_SESSION['business_email'] = $row['business_email'];
                $_SESSION['store_name'] = $row['store_name'];

                if (isset($_POST['remember'])) {
                    // Set cookies for 30 days
                    setcookie('seller_id', $row['seller_id'], time() + (86400 * 30), "/");
                    setcookie('business_email', $row['business_email'], time() + (86400 * 30), "/");
                    setcookie('store_name', $row['store_name'], time() + (86400 * 30), "/");
                }

                header("Location: dashboard.php");
                exit;
            } else {
                // Invalid password
                $error_message = "Invalid email or password";
            }
        } else {
            // Invalid email
            $error_message = "Invalid email or password";
        }
    }
}

// Check for success message
if (isset($_GET['message'])) {
    $success_message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login - Seller</title>
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
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-3">
            <div class="container">
                <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="dashboard.php" class="logo-login d-flex align-items-center w-auto ">
                                <img src="../assets/img/logo.png" alt="ShopSphere-logo">
                            </a>
                        </div>

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Login to Seller Account</h5>
                                    <p class="text-center small">Enter your username & password to login</p>
                                </div>

                                <?php if (!empty($error_message)) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($success_message)) : ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>

                                <form class="row g-3 needs-validation" action="index.php" method="post">

                                    <div class="col-12">
                                        <label for="slrEmail" class="form-label">Seller Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-envelope'></i></span>
                                            <input type="text" name="slrEmail" class="form-control" id="slrEmail" value="<?php echo isset($slrEmail) ? htmlspecialchars($slrEmail) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroupPrepend"><i class='bx bx-key'></i></span>
                                            <input type="password" name="password" class="form-control" id="password" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Login</button>
                                    </div>
                                    <div class="text-center">
                                        <a href="request_reset.php" class="small">Forgot password?</a>
                                    </div>
                                    <hr>
                                    <div class="col-12 text-center mt-0">
                                        <a class="btn btn-success" href="slr_register.php">Create an account</a>
                                    </div>
                                </form>

                            </div>
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

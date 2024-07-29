<?php
session_start();
include '../database/conn.php';

// Initialize variables to hold input values and error messages
$email = $password = "";
$emailErr = $passwordErr = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Please enter your email address.";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Please enter your password.";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));
    }

    // Proceed with login if no validation errors
    if (empty($emailErr) && empty($passwordErr)) {
        // Fetch user from database based on email
        $sql = "SELECT user_id, email, password FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $email, $hashed_password);
                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start session and handle cookies
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['email'] = $email;

                        if (isset($_POST['remember_me'])) {
                            // Set cookies for 30 days
                            setcookie('user_id', $user_id, time() + (86400 * 30), "/");
                            setcookie('email', $email, time() + (86400 * 30), "/");
                        }

                        header("Location: index.php");
                        exit();
                    } else {
                        $passwordErr = "Invalid password.";
                    }
                }
            } else {
                $emailErr = "No account found with this email.";
            }

            $stmt->close();
        } else {
            $emailErr = "Database error: " . $conn->error;
        }
    }
}

// Check if user is already logged in via cookies
if (isset($_COOKIE['user_id']) && isset($_COOKIE['email'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['email'];
    header("Location: index.php");
    exit();
}

// Function to display error messages for each field
function displayError($fieldError)
{
    if (!empty($fieldError)) {
        echo '<div class="text-danger">' . $fieldError . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Login - ShopSphere</title>
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
    <div class="container-fluid">
        <section class="min-vh-100 login-center-vertically">
            <div class="container py-4">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col col-xl-10">
                        <div class="card login-form-card mb-0">
                            <div class="row g-0">
                                <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                    <div class="card-body card-login-body p-3 p-lg-4 text-black">
                                        <h4 class="fw-normal mb-3 fw-bold text-center" style="color: #4154f1;">Login</h4>

                                        <!-- Login Form -->
                                        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="email">Email address *</label>
                                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                                                <?php displayError($emailErr); ?>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="password">Password *</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password" class="form-control" value="<?php echo htmlspecialchars($password); ?>">
                                                    <button type="button" class="btn btn-outline-secondary bi bi-eye" id="togglePassword"></button>
                                                </div>
                                                <?php displayError($passwordErr); ?>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                                                <label class="form-check-label" for="remember_me">
                                                    Remember Me
                                                </label>
                                            </div>

                                            <div class="text-center pt-1 mb-1">
                                                <button class="btn btn-primary btn-block" type="submit" name="login">Login</button>
                                            </div>
                                            <div class="text-center">
                                                <p class="mb-0" style="color: #393f81;">Don't have an account? <a href="usr_register.php" class="link-opacity-25-hover">Register</a> Or <a href="index.php" class="">Continue as Guest</a></p>
                                            </div>
                                        </form>
                                        <!-- End Login Form -->
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-5 d-none d-md-block">
                                    <div class="d-flex justify-content-center p-3 p-lg-4 mt-1">
                                        <img src="../assets/img/logo.png" alt="ShopSphere-logo" style="width: 150px;">
                                    </div>
                                    <img src="../assets/img/login-img.png" alt="login image" class="img-fluid login-img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var fieldType = passwordField.getAttribute('type');
            if (fieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
            } else {
                passwordField.setAttribute('type', 'password');
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
            }
        });
    </script>

</body>

</html>

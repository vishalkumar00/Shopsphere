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
                                        <h4 class="fw-normal mb-3 fw-bold text-center" style="color: #4154f1;">Log in to your account</h4>
                                        <!-- Display Login Error -->
                                        <?php if (isset($_SESSION['login_error'])) : ?>
                                            <div class="alert alert-danger"><?php echo $_SESSION['login_error']; ?></div>
                                            <?php unset($_SESSION['login_error']); ?>
                                        <?php endif; ?>
                                        <!-- Login Form -->
                                        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="email">Email address *</label>
                                                <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>">
                                                <?php displayError($emailErr); ?>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="password">Password *</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password" class="form-control">
                                                    <button type="button" class="btn btn-outline-secondary bi bi-eye" id="togglePassword"></button>
                                                </div>
                                                <?php displayError($passwordErr); ?>
                                            </div>

                                            <div class="text-center pt-1 mb-1">
                                                <button class="btn btn-primary btn-block" type="submit" name="login">Login</button>
                                            </div>
                                            <div class="text-center">
                                                <p class="mb-0" style="color: #393f81;">Don't have an account? <a href="usr_register.php" class="link-opacity-25-hover">Register here</a></p>
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
                          </body>
                                    </html>
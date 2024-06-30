
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Register - ShopSphere</title>
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
        <section class="min-vh-100">
            <div class="container py-4">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col col-xl-10">
                        <div class="card register-form-card mb-0" style="border-radius: 1rem;">
                            <div class="row g-0">
                                <div class="col-md-6 col-lg-5 d-none d-md-block">
                                    <div class="d-flex justify-content-center p-3 p-lg-4 mt-1">
                                        <img src="../assets/img/logo.png" alt="ShopSphere-logo" style="width: 150px;">
                                    </div>
                                    <img src="../assets/img/register-img.png" alt="register form" class="img-fluid mt-5" style="border-radius: 1rem 0 0 1rem;" />
                                </div>
                                <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                    <div class="card-body card-register-body p-3 p-lg-4 text-black">

                                        <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                                            <h4 class="fw-normal mb-3 fw-bold text-center" style="color: #4154f1;">Register your account</h4>

                                            <?php if (!empty($error_message)) : ?>
                                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                            <?php endif; ?>

                                            <div class="row mb-2">
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <label class="form-label form-label-register" for="first_name">First Name *</label>
                                                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" />
                                                        <span class="text-danger"><?php echo $first_nameErr; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-outline">
                                                        <label class="form-label form-label-register" for="last_name">Last Name *</label>
                                                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" />
                                                        <span class="text-danger"><?php echo $last_nameErr; ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="email">Email address *</label>
                                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" />
                                                <span class="text-danger"><?php echo $emailErr; ?></span>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="mobile_number">Mobile Number</label>
                                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" value="<?php echo htmlspecialchars($mobile_number); ?>" />
                                                <span class="text-danger"><?php echo $mobile_numberErr; ?></span>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="password">Password *</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password" class="form-control">
                                                    <button type="button" class="btn btn-outline-secondary bi bi-eye" id="togglePassword"></button>
                                                </div>
                                                <span class="text-danger"><?php echo $passwordErr; ?></span>
                                            </div>

                                            <div class="form-outline mb-2">
                                                <label class="form-label form-label-register" for="confirm_password">Confirm Password *</label>
                                                <div class="input-group">
                                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                                                    <button type="button" class="btn btn-outline-secondary bi bi-eye" id="toggleConfirmPassword"></button>
                                                </div>
                                                <span class="text-danger"><?php echo $confirm_passwordErr; ?></span>
                                            </div>

                                            <div class=" text-center pt-1 mb-1">
                                                <button class="btn btn-primary btn-block" type="submit" name="register">Register</button>
                                            </div>
                                            <div class="text-center">
                                                <p class="mb-0" style="color: #393f81;">Already have an account? <a href="index.php" class="link-opacity-25-hover">Login here</a></p>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePasswordVisibility('password', 'togglePassword');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
        });

        function togglePasswordVisibility(fieldId, toggleId) {
            var passwordField = document.getElementById(fieldId);
            var toggleButton = document.getElementById(toggleId);

            var fieldType = passwordField.getAttribute('type');
            if (fieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                toggleButton.classList.remove('bi-eye');
                toggleButton.classList.add('bi-eye-slash');
            } else {
                passwordField.setAttribute('type', 'password');
                toggleButton.classList.remove('bi-eye-slash');
                toggleButton.classList.add('bi-eye');
            }
        }
    </script>


</body>
</hmtl>
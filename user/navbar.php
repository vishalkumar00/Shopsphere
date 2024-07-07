<?php
// Check if a session is already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../database/conn.php';

// Check if user is logged in
$cartCount = 0;
$userFirstName = '';
$userEmail = '';
if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT first_name, email FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userFirstName = $user['first_name'];
    $userEmail = $user['email'];
  }

  // Fetch cart count
  $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $cartCount = $row['count'] ?? 0;
}

// Fetch categories from the database
$categories = [];
$categoryStmt = $conn->prepare("SELECT category_id, category_name FROM categories");
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
while ($categoryRow = $categoryResult->fetch_assoc()) {
  $categories[] = $categoryRow;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ShopSphere</title>
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
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
</head>

<body>
  <header class="header-2">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg border-bottom">
      <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand logo" href="index.php">
          <img src="../assets/img/logo.png" alt="ShopSphere">
        </a>

        <!-- Search Bar -->
        <form class="d-flex" method="GET" action="" style="flex-grow: 1; max-width: 600px; margin-left: 20px;">
          <div class="input-group">
            <input class="form-control search-input-user" type="search" placeholder="Search for products" aria-label="Search" name="query">
            <button class="btn search-button" type="submit"><i class="bi bi-search text-white"></i></button>
          </div>
        </form>

        <!-- Cart Icon -->
        <div class="navbar-nav ms-auto d-none d-sm-flex">
          <a class="nav-link nav-cart-icon" href="cart.php">
            <i class="bi bi-cart"></i>
            <span class="badge bg-primary badge-number-2"><?php echo $cartCount; ?></span>
          </a>
        </div>
      </div>
    </nav>

    <!-- Secondary Navbar -->
    <nav class="navbar navbar-expand-lg border user-second-navbar">
        <!-- Toggle Button for Mobile View -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Page Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
          <ul class="navbar-nav ps-2 me-auto fw-bold">
            <li class="nav-item nav-item-user">
              <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item nav-item-user">
              <a class="nav-link" href="shop.php">Shop</a>
            </li>
            <li class="nav-item nav-item-user">
              <a class="nav-link" href="cart.php">Cart</a>
            </li>
            <li class="nav-item nav-item-user">
              <a class="nav-link" href="checkout.php">Checkout</a>
            </li>
            <li class="nav-item nav-item-user">
              <a class="nav-link" href="contact.php">Contact</a>
            </li>
          </ul>

          <!-- User Links -->
          <ul class="navbar-nav ms-auto pe-2">
            <?php if (isset($_SESSION['user_id'])) : ?>
              <li class="nav-item-icon dropdown nav-item-user-2 pe-4">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                  <i class="ri-user-line fs-4"></i>
                  <span class="d-none d-md-block dropdown-toggle ps-1 fw-bold"><?php echo htmlspecialchars($userFirstName); ?></span>
                  <i class="bi bi-chevron-down fs-6"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                  <li class="dropdown-header text-center">
                    <h6 class="fw-bold"><?php echo htmlspecialchars($userFirstName); ?></h6>
                    <span><small><?php echo htmlspecialchars($userEmail); ?></small></span>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center justify-content-center" href="usr_logout.php">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Sign Out</span>
                    </a>
                  </li>
                </ul>
              </li>
            <?php else : ?>
              <li class="nav-item nav-item-user fw-bold">
                <a class="nav-link" href="usr_login.php">Login</a>
              </li>
              <li class="nav-item nav-item-user fw-bold">
                <a class="nav-link" href="usr_register.php">Register</a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      <!-- </div> -->
    </nav>
  </header>

  <!-- Back to Top Button -->
<button id="backToTopBtn" class="btn btn-primary">
    <i class="ri-arrow-up-line"></i>
</button>
</body>

</html>
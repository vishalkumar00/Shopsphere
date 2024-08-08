<?php
session_start();
include '../database/conn.php';

if (!isset($_SESSION['admin'])) {
  header("Location: index.php");
  exit();
}

// Set the correct time zone
date_default_timezone_set('America/Toronto');

// Function to format time as relative
function timeAgo($timestamp)
{
  $time = strtotime($timestamp);
  $currentTime = time();
  $timeDifference = $currentTime - $time;
  $seconds = $timeDifference;

  $minutes      = round($seconds / 60);           // value 60 is seconds
  $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 seconds
  $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 seconds

  if ($seconds <= 60) {
    return "Just now";
  } else if ($minutes <= 60) {
    return "$minutes mins";
  } else if ($hours <= 24) {
    return "$hours hrs";
  } else {
    return "$days days";
  }
}

// Fetch unread notifications of type 'order' for all sellers
$sql_notifications = "SELECT message, created_at FROM notifications WHERE type = 'order' AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->get_result();
$notifications = [];
while ($row = $result_notifications->fetch_assoc()) {
  $notifications[] = $row;
}
$stmt_notifications->close();

// Count unread notifications
$unread_count = count($notifications);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin - ShopSphere</title>
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
</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo">
        <img src="../assets/img/logo.png" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center m-0">

        <li class="nav-item dropdown">
          <a class="nav-link nav-icon d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number"><?php echo $unread_count; ?></span>
            <i class="bi bi-chevron-down fs-6"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              Notifications
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <?php if ($unread_count > 0) : ?>
              <?php foreach ($notifications as $notification) : ?>
                <li class="notification-item">
                  <i class="bi bi-box-seam text-primary"></i>
                  <div>
                    <h4><a href="./ad_orders.php" class="text-primary">New Order</a></h4>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <p><b><?php echo timeAgo($notification['created_at']); ?></b></p>
                  </div>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
              <?php endforeach; ?>
            <?php else : ?>
              <li class="notification-item">
                <div>
                  <p>No new notifications</p>
                </div>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
            <?php endif; ?>
          </ul>
        </li>

        <li class="nav-item-icon dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <i class="ri-admin-line fs-4"></i>
            <span class="d-none d-md-block dropdown-toggle ps-1 fw-bold">Admin</span>
            <i class="bi bi-chevron-down fs-6"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header text-center">
              <h6 class="fw-bold text-success">Admin</h6>
              <span>ShopSphere</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="">
              <a class="dropdown-item d-flex align-items-center justify-content-center" href="logout.php">
                <i class="bi bi-box-arrow-right text-danger"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header><!-- End Header -->

</body>

</html>
<?php 
include '../database/conn.php'; 
session_start();

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

// Check if the seller is logged in
if (!isset($_SESSION['seller_id']) || !isset($_SESSION['business_email'])) {
    header("Location: index.php");
    exit();
}

// Fetch the store name and email from session
$store_name = $_SESSION['store_name'];
$business_email = $_SESSION['business_email'];
$seller_id = $_SESSION['seller_id'];

// Fetch unread notifications for the seller
$sql_notifications = "SELECT message, created_at, type FROM notifications WHERE seller_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->bind_param('i', $seller_id);
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

  <title>Seller - ShopSphere</title>
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
        <img src="../assets/img/logo.png" alt="seller-shopsphere-logo">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center m-0">

      <!-- Notification feature  -->
      <li class="nav-item dropdown">
          <a class="nav-link nav-icon d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number"><?php echo $unread_count; ?></span>
            <i class="bi bi-chevron-down fs-6"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">Notifications</li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <?php foreach ($notifications as $notification): ?>
              <li class="notification-item">
                <i class="bi <?php echo $notification['type'] == 'order' ? 'bi bi-box-seam text-primary' : 'bi bi-megaphone text-warning'; ?>"></i>
                <div>
                  <h4><?php echo $notification['type'] == 'order' ? 'New Order' : 'Announcement'; ?></h4>
                  <p><?php echo htmlspecialchars($notification['message']); ?></p>
                  <p><b><?php echo timeAgo($notification['created_at']); ?></b></p>
                </div>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
            <?php endforeach; ?>
            <?php if ($unread_count === 0): ?>
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
            <i class="ri-user-star-line fs-4"></i>
            <span class="d-none d-md-block dropdown-toggle ps-1 fw-bold"><?php echo htmlspecialchars($store_name); ?></span>
            <i class="bi bi-chevron-down fs-6"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header text-center">
              <h6 class="fw-bold text-success"><?php echo htmlspecialchars($store_name); ?></h6>
              <span class="text-dark"><?php echo htmlspecialchars($business_email); ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li> 
              <a class="dropdown-item d-flex align-items-center justify-content-center" href="logout.php">
                <i class="bi bi-box-arrow-right text-danger"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

</body>

</html>

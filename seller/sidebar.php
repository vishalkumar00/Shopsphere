<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/dashboard.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="dashboard.php">
        <i class="bx bxs-dashboard fs-5"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'slr_products.php') !== false || strpos($_SERVER['REQUEST_URI'], 'ad_products.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_products.php">
        <i class='bx bx-closet'></i>
        <span>Products</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/slr_orders.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_orders.php">
        <i class="bx bxs-package fs-5"></i>
        <span>Orders</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/slr_revenue.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_revenue.php">
        <i class='bx bx-wallet'></i>
        <span>Revenue</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'slr_profile.php') !== false || strpos($_SERVER['REQUEST_URI'], 'change_password.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_profile.php">
        <i class="bx bxs-user-detail fs-5"></i>
        <span>Proile</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/announcements.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="announcements.php">
        <i class="bx bxs-megaphone fs-5"></i>
        <span>Announcements</span>
      </a>
    </li>
  </ul>
</aside>
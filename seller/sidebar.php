<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/dashboard.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="dashboard.php">
        <i class="bx bxs-dashboard fs-5"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/slr_products.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_products.php">
        <!-- <i class='bx bx-box'></i> -->
        <i class='bx bx-closet'></i>
        <span>Products</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-package fs-5"></i>
        <span>Orders</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class='bx bx-wallet'></i>
        <span>Revenue</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-inbox fs-5"></i>
        <span>Feedbacks</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/seller/slr_profile.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="slr_profile.php">
        <i class="bx bxs-user-detail fs-5"></i>
        <span>Proile</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-megaphone fs-5"></i>
        <span>Announcements</span>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-bank fs-5"></i>
        <span>Payments</span>
      </a>
    </li> -->
  </ul>
</aside>

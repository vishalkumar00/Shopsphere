<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="dashboard.php">
        <i class="bx bxs-dashboard fs-5"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/ad_category.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="ad_category.php">
        <i class="bx bx-category-alt fs-5"></i>
        <span>Category</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/ad_products.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="ad_products.php">
        <i class="bx bx-list-ul fs-5"></i>
        <span>Product List</span>
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
        <i class="bx bxs-inbox fs-5"></i>
        <span>Category Req.</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-user-detail fs-5"></i>
        <span>Sellers' List</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-megaphone fs-5"></i>
        <span>Announcements</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-bank fs-5"></i>
        <span>Payments</span>
      </a>
    </li>
  </ul>
</aside>

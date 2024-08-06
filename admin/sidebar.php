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
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'ad_orders.php') !== false || strpos($_SERVER['REQUEST_URI'], 'view_order.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="ad_orders.php">
        <i class="bx bxs-package fs-5"></i>
        <span>Orders</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/promo_code.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="promo_code.php">
        <i class="bx bxs-discount fs-5"></i>
        <span>Promo Codes</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'sellers_list.php') !== false || strpos($_SERVER['REQUEST_URI'], 'view_seller.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="sellers_list.php">
        <i class="bx bxs-user-detail fs-5"></i>
        <span>Sellers' List</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'users_list.php') !== false || strpos($_SERVER['REQUEST_URI'], 'view_user.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="users_list.php">
      <i class="bi bi-people-fill fs-6"></i>
        <span>Customers' List</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="bx bxs-megaphone fs-5"></i>
        <span>Announcements</span>
      </a>
    </li>
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'ad_revenue.php') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="ad_revenue.php">
        <i class="bx bxs-bank fs-5"></i>
        <span>Revenue</span>
      </a>
    </li>
  </ul>
</aside>

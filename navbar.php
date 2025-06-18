<style>

</style>

<nav id="sidebar" class='sidebar'>
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-chevron-left"></i>
  </button>
  
  <div class="sidebar-list">
    <a href="index.php?page=home" class="nav-item nav-home">
      <span class='icon-field'><i class="fas fa-home"></i></span>
      <span class="nav-text">Dashboard</span>
    </a>
    
    <a href="index.php?page=orders" class="nav-item nav-orders">
      <span class='icon-field'><i class="fas fa-receipt"></i></span>
      <span class="nav-text">Orders</span>
    </a>
    
    <a href="billing/index.php" class="nav-item nav-takeorders">
      <span class='icon-field'><i class="fas fa-cash-register"></i></span>
      <span class="nav-text">Billings</span>
    </a>
    
    <?php if($_SESSION['login_type'] == 1): ?>
    <div class="sidebar-section">Management</div>
    
    <a href="index.php?page=categories" class="nav-item nav-categories">
      <span class='icon-field'><i class="fas fa-tags"></i></span>
      <span class="nav-text">Categories</span>
    </a>
    
    <a href="index.php?page=products" class="nav-item nav-products">
      <span class='icon-field'><i class="fas fa-boxes"></i></span>
      <span class="nav-text">Products</span>
    </a>
    
    <div class="sidebar-section">Reports</div>
    
    <a href="index.php?page=sales_report" class="nav-item nav-sales_report">
      <span class='icon-field'><i class="fas fa-chart-line"></i></span>
      <span class="nav-text">Sales Report</span>
    </a>
    
    <div class="sidebar-section">System</div>
    
    <a href="index.php?page=users" class="nav-item nav-users">
      <span class='icon-field'><i class="fas fa-users-cog"></i></span>
      <span class="nav-text">Users</span>
    </a>
    
    <a href="index.php?page=site_settings" class="nav-item nav-site_settings">
      <span class='icon-field'><i class="fas fa-cog"></i></span>
      <span class="nav-text">Settings</span>
    </a>
    <?php endif; ?>
  </div>
</nav>

<script>
// Toggle sidebar collapse
document.getElementById('sidebarToggle').addEventListener('click', function() {
  document.body.classList.toggle('sidebar-collapsed');
  localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
});

// Check for saved state on page load
document.addEventListener('DOMContentLoaded', function() {
  if (localStorage.getItem('sidebarCollapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
  }
  
  // Set active nav item
  const currentPage = '<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>';
  if (currentPage) {
    document.querySelectorAll('.nav-item').forEach(item => {
      item.classList.remove('active');
    });
    
    const activeItems = document.querySelectorAll(`.nav-${currentPage}`);
    if (activeItems.length > 0) {
      activeItems.forEach(item => item.classList.add('active'));
    } else {
      // Fallback for pages like "add-category" that should highlight "categories"
      if (currentPage.includes('category')) {
        document.querySelector('.nav-categories')?.classList.add('active');
      } else if (currentPage.includes('product')) {
        document.querySelector('.nav-products')?.classList.add('active');
      }
    }
  }
});

// Auto-close on mobile when clicking a link
document.querySelectorAll('.sidebar-list a').forEach(link => {
  link.addEventListener('click', function() {
    if (window.innerWidth < 992) {
      document.body.classList.add('sidebar-collapsed');
      localStorage.setItem('sidebarCollapsed', 'true');
    }
  });
});

// Responsive handling
function handleResponsive() {
  if (window.innerWidth < 992) {
    document.body.classList.add('sidebar-collapsed');
  } else {
    const savedState = localStorage.getItem('sidebarCollapsed');
    document.body.classList.toggle('sidebar-collapsed', savedState === 'true');
  }
}

window.addEventListener('resize', handleResponsive);
handleResponsive(); // Initialize
</script>
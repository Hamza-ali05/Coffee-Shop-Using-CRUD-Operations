<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Coffee Shop • Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">☕ Coffee Shop</a>
    <div class="navbar-nav">
      <a class="nav-link" href="products.php">Products</a>
      <a class="nav-link" href="orders.php">Orders</a>
      <a class="nav-link" href="customers.php">Customers</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h3 class="mb-3">Dashboard</h3>
  <div class="row g-3">
    <?php
      $products = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'] ?? 0;
      $orders   = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'] ?? 0;
      $todayRev = $conn->query("SELECT COALESCE(SUM(total),0) s FROM orders WHERE DATE(order_date)=CURDATE()")->fetch_assoc()['s'] ?? 0;
    ?>
    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between"><span class="fw-medium">Products</span><span>📦</span></div>
        <div class="fs-3 fw-bold mt-2"><?php echo (int)$products; ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between"><span class="fw-medium">Orders</span><span>🧾</span></div>
        <div class="fs-3 fw-bold mt-2"><?php echo (int)$orders; ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between"><span class="fw-medium">Revenue Today</span><span>💸</span></div>
        <div class="fs-3 fw-bold mt-2">Rs. <?php echo number_format((float)$todayRev, 2); ?></div>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <a href="products.php" class="btn btn-coffee">Manage Products</a>
  </div>
</div>
</body>
</html>

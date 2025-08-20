<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Orders • Coffee Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">☕ Coffee Shop</a>
    <div class="navbar-nav">
      <a class="nav-link" href="products.php">Products</a>
      <a class="nav-link active" href="orders.php">Orders</a>
      <a class="nav-link" href="customers.php">Customers</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Orders</h3>
    <a class="btn btn-success" href="add_order.php">+ New Order</a>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['msg']); ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>ID</th><th>Customer</th><th>Total</th><th>Date</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM orders ORDER BY id DESC";
          $res = $conn->query($sql);
          if ($res->num_rows == 0) {
            echo '<tr><td colspan="5" class="text-center py-4">No orders yet.</td></tr>';
          }
          while ($row = $res->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
              <td>Rs. <?php echo number_format($row['total'],2); ?></td>
              <td><?php echo $row['order_date']; ?></td>
              <td>
                <a class="btn btn-primary btn-sm" href="edit_order.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="btn btn-danger btn-sm" href="delete_order.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this order?');">Delete</a>
                <a class="btn btn-secondary btn-sm" href="view_order.php?id=<?php echo $row['id']; ?>">View</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>

<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Products • Coffee Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">☕ Coffee Shop</a>
    <div class="navbar-nav">
      <a class="nav-link active" href="products.php">Products</a>
      <a class="nav-link" href="orders.php">Orders</a>
      <a class="nav-link" href="customers.php">Customers</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Products</h3>
    <a class="btn btn-success" href="add_product.php">+ Add Product</a>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['msg']); ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $conn->prepare("SELECT id,name,category,price,stock FROM products ORDER BY id DESC");
          $stmt->execute();
          $res = $stmt->get_result();
          if ($res->num_rows === 0) {
            echo '<tr><td colspan="6" class="text-center py-4">No products yet.</td></tr>';
          }
          while ($row = $res->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['category']); ?></td>
              <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
              <td><?php echo (int)$row['stock']; ?></td>
              <td>
                <a class="btn btn-primary btn-sm" href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="btn btn-danger btn-sm" href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; $stmt->close(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>

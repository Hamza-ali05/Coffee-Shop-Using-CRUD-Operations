<?php
include 'db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: products.php"); exit; }

$err = "";
// Fetch existing
$stmt = $conn->prepare("SELECT name,category,price,stock FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { $stmt->close(); header("Location: products.php"); exit; }
$row = $res->fetch_assoc();
$stmt->close();

$name = $row['name'];
$category = $row['category'];
$price = $row['price'];
$stock = $row['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $category = trim($_POST['category'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $stock = (int)($_POST['stock'] ?? 0);

  if ($name === "" || $category === "" || $price < 0 || $stock < 0) {
    $err = "Please fill all fields with valid values.";
  } else {
    $up = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=? WHERE id=?");
    $up->bind_param("ssdii", $name, $category, $price, $stock, $id);
    $up->execute();
    $up->close();
    header("Location: products.php?msg=" . urlencode("Product updated"));
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Product • Coffee Shop</title>
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
  <h3 class="mb-3">Edit Product</h3>
  <?php if($err): ?><div class="alert alert-danger py-2"><?php echo $err; ?></div><?php endif; ?>
  <form method="post" class="card p-3 shadow-sm" style="max-width:640px;">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($name); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Category</label>
      <input type="text" name="category" class="form-control" required value="<?php echo htmlspecialchars($category); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Price (Rs.)</label>
      <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?php echo htmlspecialchars($price); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Stock</label>
      <input type="number" min="0" name="stock" class="form-control" required value="<?php echo htmlspecialchars($stock); ?>">
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-coffee" type="submit">Update</button>
      <a class="btn btn-secondary" href="products.php">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>

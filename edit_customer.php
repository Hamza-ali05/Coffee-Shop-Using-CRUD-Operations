<?php
include 'db.php';
$id = (int)($_GET['id'] ?? 0);
$res = $conn->query("SELECT * FROM customers WHERE id=$id");
if ($res->num_rows==0){ header("Location: customers.php"); exit; }
$c = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  if ($name!=="") {
    $stmt = $conn->prepare("UPDATE customers SET name=?,phone=? WHERE id=?");
    $stmt->bind_param("ssi",$name,$phone,$id);
    $stmt->execute();
    $stmt->close();
    header("Location: customers.php?msg=" . urlencode("Customer updated"));
    exit;
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Edit Customer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"></head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark"><div class="container-fluid">
<a class="navbar-brand fw-bold" href="index.php">☕ Coffee Shop</a>
<div class="navbar-nav"><a class="nav-link" href="products.php">Products</a><a class="nav-link" href="orders.php">Orders</a><a class="nav-link" href="customers.php">Customers</a></div>
</div></nav>
<div class="container py-4">
<h3>Edit Customer</h3>
<form method="post" class="card p-3 shadow-sm" style="max-width:500px;">
  <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($c['name']); ?>"></div>
  <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($c['phone']); ?>"></div>
  <button type="submit" class="btn btn-coffee">Update</button>
  <a href="customers.php" class="btn btn-secondary">Cancel</a>
</form>
</div></body></html>

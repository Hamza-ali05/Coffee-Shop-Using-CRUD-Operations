<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  if ($name!=="") {
    $stmt = $conn->prepare("INSERT INTO customers (name,phone) VALUES (?,?)");
    $stmt->bind_param("ss",$name,$phone);
    $stmt->execute();
    $stmt->close();
    header("Location: customers.php?msg=" . urlencode("Customer added"));
    exit;
  }
}
?>
<!DOCTYPE html>
<html><head>
<meta charset="utf-8"><title>Add Customer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"></head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark"><div class="container-fluid">
<a class="navbar-brand fw-bold" href="index.php">☕ Coffee Shop</a>
<div class="navbar-nav"><a class="nav-link" href="products.php">Products</a><a class="nav-link" href="orders.php">Orders</a><a class="nav-link" href="customers.php">Customers</a></div>
</div></nav>
<div class="container py-4">
<h3>Add Customer</h3>
<form method="post" class="card p-3 shadow-sm" style="max-width:500px;">
  <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
  <button type="submit" class="btn btn-coffee">Save</button>
  <a href="customers.php" class="btn btn-secondary">Cancel</a>
</form>
</div></body></html>

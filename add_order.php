<?php
include 'db.php';

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customer_id = (int)($_POST['customer_id'] ?? 0);
  $items = $_POST['items'] ?? []; // product_id => qty

  if ($customer_id <= 0 || empty($items)) {
    $err = "Please select a customer and at least one product.";
  } else {
    $total = 0;
    foreach ($items as $pid => $qty) {
      $pid = (int)$pid;
      $qty = (int)$qty;
      if ($qty > 0) {
        $res = $conn->query("SELECT price FROM products WHERE id=$pid");
        if ($row = $res->fetch_assoc()) {
          $total += $row['price'] * $qty;
        }
      }
    }

    // get customer name
    $cRes = $conn->query("SELECT name FROM customers WHERE id=$customer_id");
    $cRow = $cRes->fetch_assoc();
    $customer_name = $cRow['name'];

    // insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_name,total) VALUES (?,?)");
    $stmt->bind_param("sd", $customer_name, $total);
    if ($stmt->execute()) {
      $order_id = $stmt->insert_id;
      $stmt->close();

      // insert items
      foreach ($items as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty > 0) {
          $stmt = $conn->prepare("INSERT INTO order_items (order_id,product_id,quantity) VALUES (?,?,?)");
          $stmt->bind_param("iii", $order_id, $pid, $qty);
          $stmt->execute();
          $stmt->close();
        }
      }

      // ✅ No print_bill redirection here anymore

    } else {
      echo "Error: " . $stmt->error;
    }
  }
}

// fetch existing customers and products
$customers = $conn->query("SELECT id,name FROM customers ORDER BY name ASC");
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Order • Coffee Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    .select2-container .select2-selection--single {
      height: 38px;
      padding: 6px 12px;
    }
  </style>
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
  <h3 class="mb-3">New Order</h3>
  <?php if($err): ?><div class="alert alert-danger py-2"><?php echo $err; ?></div><?php endif; ?>
  <form method="post" class="card p-3 shadow-sm">

    <!-- Customer Search -->
    <div class="mb-3">
      <label class="form-label">Select Customer</label>
      <select name="customer_id" class="form-select select2" required>
        <option value="">-- Choose Customer --</option>
        <?php while ($c = $customers->fetch_assoc()): ?>
          <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Product Search -->
    <h5>Products</h5>
    <?php while ($p = $products->fetch_assoc()): ?>
      <div class="mb-2 row">
        <label class="col-sm-4 col-form-label"><?php echo htmlspecialchars($p['name']); ?> (Rs.<?php echo $p['price']; ?>)</label>
        <div class="col-sm-3">
          <input type="number" min="0" name="items[<?php echo $p['id']; ?>]" class="form-control" value="0">
        </div>
      </div>
    <?php endwhile; ?>

    <button type="submit" class="btn btn-coffee mt-3">Save Order</button>
    <a href="orders.php" class="btn btn-secondary mt-3">Cancel</a>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('.select2').select2();
  });
</script>
</body>
</html>

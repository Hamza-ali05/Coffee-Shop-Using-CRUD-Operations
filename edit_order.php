<?php
include 'db.php';

// 1) Validate order id
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
  header("Location: orders.php");
  exit;
}
$order_id = (int)$_GET['id'];

// 2) Load the order (id, customer_name, product_name, quantity, total, order_date)
$ordStmt = $conn->prepare("SELECT id, customer_name, product_name, quantity, total, order_date FROM orders WHERE id = ?");
$ordStmt->bind_param("i", $order_id);
$ordStmt->execute();
$orderRes = $ordStmt->get_result();
$order = $orderRes->fetch_assoc();
$ordStmt->close();

if (!$order) {
  die("Order not found.");
}

// 3) Load customers (names only, since orders.customer_name stores the name)
$customers = [];
$cRes = $conn->query("SELECT name FROM customers ORDER BY id DESC");
while ($row = $cRes->fetch_assoc()) {
  $customers[] = $row['name'];
}

// 4) Load products (name + price)
$products = [];
$pRes = $conn->query("SELECT name, price FROM products ORDER BY name ASC");
while ($row = $pRes->fetch_assoc()) {
  $products[] = $row; // ['name' => ..., 'price' => ...]
}

// Helper: find price for a product name
function findProductPriceByName($products, $name) {
  foreach ($products as $p) {
    if ($p['name'] === $name) return (float)$p['price'];
  }
  return null;
}

// 5) Handle POST (update)
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customer_name = trim($_POST['customer_name'] ?? '');
  $product_name  = trim($_POST['product_name'] ?? '');
  $quantity      = (int)($_POST['quantity'] ?? 0);

  if ($customer_name === "" || $product_name === "" || $quantity <= 0) {
    $err = "Please select a customer, a product, and enter a valid quantity.";
  } else {
    // Look up price for chosen product_name
    $priceStmt = $conn->prepare("SELECT price FROM products WHERE name = ? LIMIT 1");
    $priceStmt->bind_param("s", $product_name);
    $priceStmt->execute();
    $priceRes = $priceStmt->get_result();
    $priceRow = $priceRes->fetch_assoc();
    $priceStmt->close();

    if (!$priceRow) {
      $err = "Selected product not found in products table.";
    } else {
      $unit_price = (float)$priceRow['price'];
      $total = $unit_price * $quantity;

      // Update order (order_date remains unchanged)
      $upd = $conn->prepare("UPDATE orders 
                             SET customer_name = ?, product_name = ?, quantity = ?, total = ?
                             WHERE id = ?");
      $upd->bind_param("ssidi", $customer_name, $product_name, $quantity, $total, $order_id);

      if ($upd->execute()) {
        $upd->close();
        header("Location: orders.php?msg=" . urlencode("Order updated"));
        exit;
      } else {
        $err = "Failed to update order. " . $conn->error;
        $upd->close();
      }
    }
  }

  // If error, keep the posted values in the $order array so the form stays filled
  if ($err) {
    $order['customer_name'] = $customer_name;
    $order['product_name']  = $product_name;
    $order['quantity']      = $quantity;
    // Recompute a display total from the price map if possible
    $display_price = findProductPriceByName($products, $product_name);
    $order['total'] = $display_price !== null ? $display_price * max(1, $quantity) : $order['total'];
  }
}

// Build a quick product->price map for the front-end total preview
$productPriceMap = [];
foreach ($products as $p) {
  $productPriceMap[$p['name']] = (float)$p['price'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Order • Coffee Shop</title>
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
    <h3>Edit Order</h3>
    <a class="btn btn-secondary" href="orders.php">Back</a>
  </div>

  <?php if ($err): ?>
    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>

  <form method="post" class="card p-3 shadow-sm" style="max-width: 700px;">
    <div class="row g-3">
      <!-- Customer -->
      <div class="col-md-6">
        <label class="form-label">Customer</label>
        <select name="customer_name" class="form-select" required>
          <?php
            // Ensure the current order's customer is available even if removed from customers table
            $hasCurrentCustomer = in_array($order['customer_name'], $customers, true);
            if (!$hasCurrentCustomer && $order['customer_name'] !== "") {
              echo '<option value="'.htmlspecialchars($order['customer_name']).'" selected>'.
                    htmlspecialchars($order['customer_name']).' (not in list)</option>';
              echo '<option disabled>──────────</option>';
            }
          ?>
          <option value="">— Select Customer —</option>
          <?php foreach ($customers as $cname): ?>
            <option value="<?php echo htmlspecialchars($cname); ?>"
              <?php if ($cname === $order['customer_name']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($cname); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Product -->
      <div class="col-md-6">
        <label class="form-label">Product</label>
        <select name="product_name" id="product_name" class="form-select" required>
          <?php
            // Ensure current product appears even if no longer in products table
            $names = array_column($products, 'name');
            $hasCurrentProduct = in_array($order['product_name'], $names, true);
            if (!$hasCurrentProduct && $order['product_name'] !== "") {
              echo '<option value="'.htmlspecialchars($order['product_name']).'" selected>'.
                    htmlspecialchars($order['product_name']).' (not in list)</option>';
              echo '<option disabled>──────────</option>';
            }
          ?>
          <option value="">— Select Product —</option>
          <?php foreach ($products as $p): ?>
            <option 
              value="<?php echo htmlspecialchars($p['name']); ?>"
              data-price="<?php echo htmlspecialchars($p['price']); ?>"
              <?php if ($p['name'] === $order['product_name']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($p['name']); ?> (Rs.<?php echo htmlspecialchars($p['price']); ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Quantity -->
      <div class="col-md-4">
        <label class="form-label">Quantity</label>
        <input type="number" min="1" step="1" name="quantity" id="quantity" class="form-control"
               value="<?php echo (int)$order['quantity']; ?>" required>
      </div>

      <!-- Read-only Total preview (server will still recalc on submit) -->
      <div class="col-md-4">
        <label class="form-label">Total (Rs.)</label>
        <input type="text" id="total_preview" class="form-control" value="<?php echo number_format((float)$order['total'], 2); ?>" readonly>
        <div class="form-text">Auto-calculated from product price × quantity</div>
      </div>

      <!-- Order at -->
      <div class="col-md-4">
        <label class="form-label">Order At</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['order_date']); ?>" readonly>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-coffee">Update Order</button>
      <a href="orders.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
// Simple live total preview (server still validates & recalculates)
(function(){
  var priceMap = <?php echo json_encode($productPriceMap, JSON_UNESCAPED_UNICODE); ?>;
  var productSel = document.getElementById('product_name');
  var qtyInput   = document.getElementById('quantity');
  var totalBox   = document.getElementById('total_preview');

  function recalc() {
    var pname = productSel.value;
    var qty = parseInt(qtyInput.value || "0", 10);
    var price = priceMap[pname] || 0;
    var total = (qty > 0 ? qty : 0) * price;
    totalBox.value = total.toFixed(2);
  }

  productSel.addEventListener('change', recalc);
  qtyInput.addEventListener('input', recalc);
})();
</script>
</body>
</html>

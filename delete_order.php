<?php
include 'db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $conn->query("DELETE FROM orders WHERE id=$id"); // will also delete items because of FK cascade
}
header("Location: orders.php?msg=" . urlencode("Order deleted"));
exit;

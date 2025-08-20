<?php
include 'db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}
header("Location: products.php?msg=" . urlencode("Product deleted"));
exit;

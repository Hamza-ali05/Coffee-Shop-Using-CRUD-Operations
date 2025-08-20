<?php
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Delete customer from the table
    $sql = "DELETE FROM customers WHERE id = $id";
    if ($conn->query($sql)) {
        // Redirect back with success message
        header("Location: customers.php?msg=" . urlencode("Customer deleted successfully"));
        exit;
    } else {
        // Redirect back with error message
        header("Location: customers.php?msg=" . urlencode("Error deleting customer: " . $conn->error));
        exit;
    }
} else {
    // Invalid ID case
    header("Location: customers.php?msg=" . urlencode("Invalid customer ID"));
    exit;
}
?>

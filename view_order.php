<?php
include 'db.php';

// Fetch all orders
$sql = "SELECT id, customer_name, product_name, quantity, total, order_date FROM orders ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fdf6f0; /* light coffee background */
        }
        .navbar {
            background-color: #6f4e37 !important; /* coffee brown */
        }
        .table-container {
            margin: 30px auto;
            max-width: 90%;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #6f4e37; /* coffee brown heading */
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-edit {
            background-color: #8b5e3c; /* lighter brown */
            color: white;
        }
        .btn-delete {
            background-color: #d2691e; /* reddish brown */
            color: white;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Coffee Shop</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="view_order.php">View Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_order.php">Add Order</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Orders Table -->
    <div class="container table-container">
        <h2>All Orders</h2>
        <table class="table table-bordered table-striped">
            <thead style="background-color:#deb887; color:#fff;"> <!-- light brown header -->
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total (Rs.)</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo number_format($row['total'], 2); ?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td>
                                <a href="edit_order.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="delete_order.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure to delete this order?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No orders found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

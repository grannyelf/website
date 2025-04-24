<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

// Fetch orders with user information including address
$stmt = $conn->prepare("
    SELECT 
        orders.id AS order_id,
        orders.total_amount,
        orders.status,
        orders.created_at,
        users.full_name,
        users.address,
        products.name AS product_name,
        order_items.quantity
    FROM orders
    INNER JOIN users ON orders.user_id = users.id
    INNER JOIN order_items ON orders.id = order_items.order_id
    INNER JOIN products ON order_items.product_id = products.id
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fffaf5;
            margin: 0;
            padding: 2em;
        }
        header {
            background: #f78ca2;
            color: white;
            padding: 1em;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 1em;
            text-decoration: none;
        }
        h1 {
            margin: 0;
        }
        table {
            width: 100%;
            margin-top: 2em;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1em;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background: #fdd1dd;
        }
        .status {
            font-weight: bold;
            color: #f78ca2;
        }
    </style>
</head>
<body>

<header>
    <h1>All Orders</h1>
    <nav>
        <a href="admin_dashb.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>
</main>

</body>
</html>

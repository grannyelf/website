<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

// Fetch orders with user information including address
$stmt = $conn->prepare("SELECT orders.*, users.full_name, users.address FROM orders INNER JOIN users ON orders.user_id = users.id");
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
            <th>User</th>
            <th>Product</th>
            <th>Date</th>
            <th>Status</th>
            <th>Address</th> <!-- Add Address column -->
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td> <!-- User's full name -->
                <td><?= htmlspecialchars($row['product_name'] ?? 'N/A') ?></td>
                <td><?= date("F d, Y", strtotime($row['order_date'])) ?></td>
                <td class="status"><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td> <!-- User's address -->
            </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="5">No orders found.</td>
            </tr>
        <?php endif; ?>
    </table>
</main>

</body>
</html>

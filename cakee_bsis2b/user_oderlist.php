<?php
session_start();
include 'database.php';

// Make sure the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$userId = $_SESSION['user_id'];

// Get orders with products
$stmt = $conn->prepare("
    SELECT 
        orders.id AS order_id,
        orders.total_amount,
        orders.status,
        orders.created_at,
        products.name AS product_name,
        products.image AS product_image,
        order_items.quantity
    FROM orders
    INNER JOIN order_items ON orders.id = order_items.order_id
    INNER JOIN products ON order_items.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.created_at DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['info'] = [
        'total' => $row['total_amount'],
        'status' => $row['status'],
        'created_at' => $row['created_at'],
    ];
    $orders[$row['order_id']]['items'][] = [
        'name' => $row['product_name'],
        'qty' => $row['quantity'],
        'image' => $row['product_image']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders - Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fff8f0;
            margin: 0;
            padding: 2em;
        }

        h2 {
            text-align: center;
            color: #d94a76;
            margin-bottom: 1.5em;
        }

        .order-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .order-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5em 2em;
            margin-bottom: 2em;
            transition: transform 0.2s;
        }

        .order-card:hover {
            transform: scale(1.01);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1em;
        }

        .order-status {
            padding: 0.4em 1em;
            background: #fdd1dd;
            color: #d94a76;
            border-radius: 12px;
            font-weight: bold;
        }

        .order-info p {
            margin: 0.2em 0;
            color: #444;
        }

        .product-list {
            margin-top: 1em;
            padding-left: 1em;
        }

        .product-list li {
            margin-bottom: 0.3em;
        }

        .total-price {
            font-weight: bold;
            font-size: 1.1em;
            color: #f78ca2;
            margin-top: 1em;
        }

        a.back {
            display: block;
            margin-top: 2em;
            text-align: center;
            color: #d94a76;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="order-container">
    <h2>📋 My Orders</h2>

    <?php if (empty($orders)): ?>
        <p style="text-align:center;">You haven't placed any orders yet. <a href="user_dashb.php">Shop now</a>!</p>
    <?php else: ?>
        <?php foreach ($orders as $id => $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <h3>Order #<?= $id ?></h3>
                    <div class="order-status"><?= htmlspecialchars($order['info']['status']) ?></div>
                </div>

                <div class="order-info">
                    <p><strong>Date:</strong> <?= date("F d, Y", strtotime($order['info']['created_at'])) ?></p>
                </div>

                <ul class="product-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <li style="display: flex; align-items: center; margin-bottom: 0.8em;">
                        <img src="products/<?= htmlspecialchars($item['image']) ?>" alt="product image" style="height: 50px; width: 50px; border-radius: 8px; object-fit: cover; margin-right: 1em;">
                        <span><?= $item['qty'] ?> × <?= htmlspecialchars($item['name']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <p class="total-price">Total: ₱<?= number_format($order['info']['total'], 2) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a class="back" href="user_dashb.php">← Back to Shop</a>
</div>

</body>
</html>
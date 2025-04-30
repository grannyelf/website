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
        o.id AS order_id,
        o.total_amount,
        o.status,
        o.created_at,
        p.name AS product_name,
        p.image AS product_image,
        oi.quantity,
        NULL AS custom_message,
        p.price AS item_price
    FROM orders o
    INNER JOIN order_items oi ON o.id = oi.order_id
    INNER JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?

    UNION ALL

    SELECT 
        o.id AS order_id,
        o.total_amount,
        o.status,
        o.created_at,
        CONCAT('Customized: ', co.custom_message) AS product_name,
        'custom_cake.png' AS product_image,
        1 AS quantity,
        co.custom_message,
        co.price AS item_price
    FROM orders o
    INNER JOIN custom_orders co ON o.id = co.order_id
    WHERE o.user_id = ?

    ORDER BY created_at DESC
");

$stmt->bind_param("ii", $userId, $userId);

$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['info'] = [
        'id' => $row['order_id'],
        'total' => $row['total_amount'],
        'status' => $row['status'],
        'created_at' => $row['created_at'],
    ];
    $orders[$row['order_id']]['items'][] = [
        'name' => $row['product_name'],
        'qty' => $row['quantity'],
        'image' => $row['product_image'],
        'custom_message' => $row['custom_message'] ?? null,
        'price' => $row['item_price'] ?? 0
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
            margin-bottom: 2em;
            text-align: center;
            color: #d94a76;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="order-container">
    <h2>📋 My Orders</h2>
    <a class="back" href="user_dashb.php">← Back to Shop</a>

    <?php if (empty($orders)): ?>
        <p style="text-align:center;">You haven't placed any orders yet. <a href="user_dashb.php">Shop now</a>!</p>
    <?php else: ?>
        <?php foreach ($orders as $id => $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-status"><?= htmlspecialchars($order['info']['status']) ?></div>
                </div>

                <div class="order-info">
                    <p><strong>Order #: <?= $order['info']['id'] ?></strong></p>
                    <p><strong>Date:</strong> <?= date("F d, Y", strtotime($order['info']['created_at'])) ?></p>
                </div>

                <ul class="product-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <li style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 0.8em;">
                            <?php if (!empty($item['image'])): ?>
                                <div style="display: flex; align-items: center;">
                                    <img src="products/<?= htmlspecialchars($item['image']) ?>" alt="product image" style="height: 50px; width: 50px; border-radius: 8px; object-fit: cover; margin-right: 1em;">
                                    <span><?= $item['qty'] ?> × <?= htmlspecialchars($item['name']) ?></span>
                                </div>
                            <?php else: ?>
                                <span style="margin-left: 0.5em;">🎂 <?= $item['qty'] ?> × <?= htmlspecialchars($item['name']) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="total-price">Total: ₱<?= number_format($order['info']['total'], 2) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<a class="back" href="user_dashb.php">← Back to Shop</a>
</body>
</html>
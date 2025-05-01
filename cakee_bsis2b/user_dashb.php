<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Cakee Cakery</title>
    <link rel="icon" href="products/cakee yarn.png">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff8f0;
            margin: 0;
            padding: 0;
        }

        header {
            background: #f78ca2;
            color: white;
            padding: 1em;
            text-align: center;
        }

        nav {
            margin-top: 1em;
        }

        nav a {
            color: white;
            margin: 0 1em;
            text-decoration: none;
            font-weight: bold;
        }

        .welcome {
            text-align: center;
            padding: 1.5em;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5em;
            padding: 2em;
        }

        .product-card {
            background: white;
            border: 1px solid #f0c7d1;
            border-radius: 12px;
            width:350px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            transition: 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.03);
        }

        .product-card img {
            width: 100%;
            height: 350px;
            border-radius: 12px 12px 0 0;
        }

        .product-info {
            padding: 1em;
        }

        .buy-btn {
            background: #f78ca2;
            border: none;
            color: white;
            padding: 0.5em 1em;
            margin-top: 0.5em;
            border-radius: 8px;
            cursor: pointer;
        }

        footer {
            background: #f78ca2;
            color: white;
            bottom: 0;
            left: 0;
            width: 100%;
            position: fixed;
            text-align: center;
            padding: 0.5em;
        }
    </style>
</head>
<body>

<header>
    <h1>Cakee Cakery</h1>
    <nav>
        <a href="about.php">About</a>
        <a href="user_help.php">Help Support</a>
        <a href="cart.php">My Cart</a>
        <a href="user_oderlist.php">Order List</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="welcome">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>! 🎂</h2>
    <p>Explore our sweet selection of cakes below:</p>
</div>
    <form method="get" style="text-align:center; margin-bottom: 1em;">
        <input type="text" name="search" placeholder="Search cakes..." value="<?= htmlspecialchars($search ?? '') ?>" style="padding: 0.5em; width: 200px;">
        <button type="submit" style="padding: 0.5em; background:#f78ca2; border:none; color:white;">Search</button>
    </form>
<div class="product-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="products/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="product-info">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p>₱<?= number_format($row['price'], 2) ?></p>
                <p><strong>Stock:</strong> <?= $row['stock'] ?></p>

            <?php if ($row['stock'] > 0): ?>
            <form action="add_cart.php" method="post">
                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                <input type="number" name="quantity" min="1" max="<?= $row['stock'] ?>" value="1" style="width: 50px;">
                <button type="submit" class="buy-btn">Add</button>
            </form>
            <?php else: ?>
            <button class="buy-btn" disabled style="background:gray;">Out of Stock</button>
            <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<footer>
    &copy; <?= date("Y") ?> Cakee Cakery. All rights reserved.
</footer>

</body>
</html>

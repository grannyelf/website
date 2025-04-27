<?php
session_start();
include 'database.php';

$result = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Cakes - Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
    <style>
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
    </style>
</head>
<body>
    <header>
        <h1>Our Cakes</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>
    <main>
    <div class="product-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="products/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="product-info">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><strong>Stock:</strong> <?= $row['stock'] > 0 ? $row['stock'] : '<span style="color:red;">Out of stock</span>' ?></p>
                <p>₱<?= number_format($row['price'], 2) ?></p>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Cake. All rights reserved.</p>
    </footer>
</body>
</html>
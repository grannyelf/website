<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List - Admin | Cakee Cakery</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #fff8f0;
            color: #333;
        }

        header {
            background-color: #f78ca2;
            color: white;
            padding: 1em 2em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 1em;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            padding: 2em;
        }

        h2 {
            color: #f78ca2;
            margin-bottom: 1em;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 1em;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #fde2e8;
            color: #333;
        }

        tr:hover {
            background-color: #fff0f4;
        }

        button.delete-btn {
            background-color: #f56565;
            color: white;
            border: none;
            padding: 0.4em 0.8em;
            border-radius: 6px;
            font-size: 0.9em;
            cursor: pointer;
        }

        button.delete-btn:hover {
            background-color: #e53e3e;
        }

            .product-img {
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
        }

        .delete-btn {
            background-color: #f56565;
            color: white;
            padding: 0.3em 0.7em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #e53e3e;
        }
        .product-img {
            height: 80px;
            border-radius: 10px;
        }
        .action-btn {
            background-color: #38a169;
            color: white;
            padding: 0.3em 0.6em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #2f855a;
        }
    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard - Cakee Cakery</h1>
    <nav>
        <a href="admin_dashb.php">Dashboard</a>
        <a href="add-product.php">Add Product</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>🍰 Product List</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created</th>
                <th>Updated</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="products/<?= htmlspecialchars($row['image']) ?>" alt="" class="product-img"></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <form action="update_stock.php" method="post" style="display: inline-flex; gap: 0.5em;">
                            <input type="number" name="new_stock" value="<?= $row['stock'] ?>" min="0" style="width: 60px; padding: 0.2em;">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="action-btn">Update</button>
                        </form>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= $row['updated_at'] ?></td>
                    <td>
                        <form method="post" action="productdel.php" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="delete-btn">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

</body>
</html>

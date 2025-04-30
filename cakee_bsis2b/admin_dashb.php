<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cakee</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #fff8f0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #f78ca2;
            color: white;
            padding: 1.5em 2em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background-color: #e53e3e;
            padding: 0.5em 1em;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .logout-link:hover {
            background-color: #c53030;
        }

        .dashboard-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2em;
        }

        .admin-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5em;
            max-width: 800px;
            width: 100%;
        }

        .admin-buttons a {
            display: block;
            background-color: #f78ca2;
            color: white;
            padding: 1em;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .admin-buttons a:hover {
            background-color: #f45b7d;
        }

        footer {
            text-align: center;
            padding: 1em;
            background: #fde2e8;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <h2>🎂 Welcome, Admin <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
        <a class="logout-link" href="logout.php">Logout</a>
    </header>

    <div class="dashboard-container">
        <div class="admin-buttons">
            <a href="add-product.php">Add Product</a>
            <a href="user_orders.php">View Orders</a>
            <a href="productlist.php">View Products</a>
            <a href="manage_user.php">Manage Users</a>
            <a href="admin_help.php">Help Request</a>
        </div>
    </div>

    <footer>
        &copy; <?= date("Y") ?> Cakee Cakery Admin Panel
    </footer>
</body>
</html>

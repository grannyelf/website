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
        }

        header {
            background-color: #f78ca2;
            color: white;
            padding: 1.5em;
            text-align: center;
        }

        nav {
            margin-top: 1em;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 1.5em;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            color: #f78ca2;
            margin-top: 1em;
        }

        .container {
            padding: 2em;
            max-width: 1000px;
            margin: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2em;
            background: white;
        }

        th, td {
            padding: 1em;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        th {
            background: #fdd1dd;
            color: #333;
        }

        .action-btn {
            padding: 0.5em 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .edit-btn {
            background-color: #fcb1bd;
            color: white;
        }

        .delete-btn {
            background-color: #f56565;
            color: white;
        }

        .add-product-btn {
            display: inline-block;
            background-color: #f78ca2;
            color: white;
            padding: 1em 2em;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin: 1em 0;
        }
    </style>
</head>
<body>
    <header>
        <h2>Welcome, Admin <?= $_SESSION['name'] ?>!</h2>
        <nav>
            <a href="add-product.php">Add Product</a>
            <a href="user_orders.php">View Orders</a>
            <a href="productlist.php">View Products</a>
            <a href="manage_user.php">Manage Users</a>
            <a href="admin_help.php">Help Request</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <p>Welcome, Admin! Use the navigation above to manage your cakery.</p>
    </main>
</body>
</html>
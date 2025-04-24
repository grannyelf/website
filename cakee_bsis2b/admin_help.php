<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$query = "SELECT help_requests.*, users.full_name FROM help_requests
          JOIN users ON help_requests.user_id = users.id
          ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en"></html>
<meta charset="UTF-8">
    <title>User Dashboard - Cakee Cakery</title>
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

    nav {
        margin-bottom: 1.5em;
        text-align: center;
    }

    nav a {
        color: white;
        margin: 0 1em;
        text-decoration: none;
        font-weight: bold;
    }

    table {
        width: 100%;
        margin-top: 1.5em;
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

    .action-btn {
        padding: 0.4em 1em;
        background-color: #f78ca2;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .action-btn:hover {
        background-color: #f45b7d;
    }

    .status {
        font-weight: bold;
        color: #f78ca2;
    }

    textarea {
        width: 100%;
        padding: 1em;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 1em;
        font-family: Arial, sans-serif;
    }
</style>
<body>
<h2>User Help Requests. <a href="admin_dashb.php">Go back?</a></h2>
<table>
    <tr><th>User</th><th>Message</th><th>Time</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['full_name'] ?></td>
        <td><?= $row['message'] ?></td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
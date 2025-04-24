<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = $_POST['message'];
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO help_requests (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $msg);
    $stmt->execute();
    echo "Help request sent!";
}
?>

<!DOCTYPE html>
<html lang="en"></html>
<head>
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
        padding: 0.2em;
        text-align: center;
        margin-bottom: 20px;
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

    button {
        padding: 0.5em 1em;
        background-color: #f78ca2;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #f45b7d;
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
</head>
<body>

<header>
    <h1>Feedback or Account Removal</h1>
    <nav>
        <a href="user_dashb.php">Go back?</a>
    </nav>
</header>

<form method="post">
    <textarea name="message" required placeholder="Describe your issue here..."></textarea><br>
    <button type="submit">Send Help Request</button>
</form>
</body>
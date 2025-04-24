<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$result = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Cakee Cakery</h1>
        <nav>
            <a href="user_dashb.php">Home</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h2>About Us!</h2>
            <p>Check out our Discord Server for more info about our website! <a href="https://youtu.be/xvFZjo5PgG0?si=GYHpK69UnOakzPLO">https://discord.gg/FreeSource</a></p>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Cakee Cakery. All rights reserved.</p>
    </footer>
</body>
</html>
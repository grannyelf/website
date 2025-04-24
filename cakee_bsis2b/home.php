<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        footer {
            background: #f78ca2;
            color: white;
            bottom: 0;
            left: 0;
            width: 100%;
            position: fixed;
            text-align: center;
            padding: 0.1em;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cakee Cakery</h1>
        <nav>
            <a href="products.php">Products</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
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
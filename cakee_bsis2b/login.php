<?php
session_start();
include 'database.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['full_name'];
            
            header("Location: " . ($row['role'] === 'admin' ? "admin_dashb.php" : "user_dashb.php"));
            exit;
        } else {
            $message = "Invalid credentials!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Cakee</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f7e5e0;
        }

        .form-box {
            background: white;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #f78ca2;
            margin-bottom: 1.5em;
        }

        input[type="text"], input[type="password"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 0.8em;
            margin: 0.6em 0;
            border: 1px solid #f0c7d1;
            border-radius: 8px;
            font-size: 1em;
        }

        button[type="submit"] {
            width: 100%;
            padding: 1em;
            background-color: #f78ca2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2em;
        }

        button[type="submit"]:hover {
            background-color: #f56c8d;
        }

        form a {
           text-align: center;
            display: block;
            margin-top: 1em;
            color: #f78ca2;
            text-decoration: none;
        }

        form a:hover {
            text-decoration: underline;
        }

        /* Make it responsive */
        @media (max-width: 600px) {
            .form-box {
                padding: 1.5em;
        }
    }
    </style>

</head>
<body>
<div class="form-container">
    <div class="form-box">
    <header>
        <h1>Login to Your Account</h1>
        <nav>
            <a href="home.php">Go Back?</a>
        </nav>
    </header>
    <main>
        <form action="login.php" method="post">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p><a href="register.php">Register here</a>
        </form>
    <p><?= $message ?></p>
    </main>
    </div>
</div>
</body>
</html>
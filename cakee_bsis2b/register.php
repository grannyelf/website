<?php
include 'database.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form inputs
    $full_name = $_POST['full_name'];  // Full name of the user
    $email = $_POST['email'];
    $password = $_POST['password'];    // Password entered by user
    $address = $_POST['address'];      // User's home address
    $phone = $_POST['phone'];      // User's phone numba
    
    // Ensure the password is not empty before processing
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password for secure storage

        $role = 'user'; // default role

        // Prepare and execute the query to insert into the users table
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, address, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $email, $hashed_password, $address, $phone, $role);
        if ($stmt->execute()) {
            $message = "Registered successfully!";
        } else {
            $message = "Registration failed: " . $conn->error;
        }
    } else {
        $message = "Password cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Cakee Cakery</title>
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
            <h1>Create a New Account</h1>
            <nav>
                <a href="home.php">Go Back?</a>
            </nav>
        </header>
        <main>
            <form action="register.php" method="post">
                <label for="full_name">Usernamer:</label>
                <input type="text" name="full_name" placeholder="Username" required>
                <label for="email">Email Address:</label>
                <input type="email" name="email" placeholder="Email" required>
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Password" required>
                <label for="address">Home Address:</label>
                <input type="text" name="address" placeholder="Home Address" required>
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" required>
                <button type="submit">Register</button>
                <a href="login.php">Already have an account? Login here</a>
            </form>
        </form>
            <p><?= $message ?></p>
        </main>
    </div>
</div>
</body>
</html>
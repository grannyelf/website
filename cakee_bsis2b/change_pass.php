<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$userId = $_GET['id'] ?? null;
$success = false;

if (!$userId) {
    header("Location: manage_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change User Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fffaf5;
            padding: 2em;
        }
        form {
            background: white;
            padding: 2em;
            border-radius: 8px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="password"] {
            width: 100%;
            padding: 0.8em;
            margin-bottom: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #f78ca2;
            border: none;
            color: white;
            padding: 0.8em 1.5em;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #f45b7d;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 1em;
            margin-bottom: 1em;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">🔐 Change User Password</h2>
    <a href="manage_user.php" style="text-align: center; display: block;">Go Back?</a>

    <?php if ($success): ?>
        <div class="success">✅ Password changed successfully!</div>
    <?php endif; ?>

    <form method="post">
        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br>
        <button type="submit">Update Password</button>
    </form>
</body>
</html>

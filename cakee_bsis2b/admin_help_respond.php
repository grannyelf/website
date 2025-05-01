<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $helpId = $_POST['help_id'];
    $response = $_POST['response'];

    $stmt = $conn->prepare("UPDATE help_requests SET response = ?, status = 'Resolved' WHERE id = ?");
    $stmt->bind_param("si", $response, $helpId);
    $stmt->execute();
}

header("Location: admin_help.php");
exit;
?>

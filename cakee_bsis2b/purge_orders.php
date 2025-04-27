<?php
session_start();
include 'database.php';

// Only admins can do this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Delete only orders marked as 'Done' and their order_items
$conn->query("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE status = 'Done')");
$conn->query("DELETE FROM orders WHERE status = 'Done'");

header("Location: user_orders.php");
exit;
?>

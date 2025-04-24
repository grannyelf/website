<?php
session_start();
include 'database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Get POST data
$delivery = $_POST['delivery'];
$payment = $_POST['payment'];

// Calculate total from cart
$cart = $_SESSION['cart'] ?? [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $total += $row['price'] * $cart[$row['id']];
    }
}

// Insert order into orders table
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, delivery, payment, total, order_date)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("issd", $user_id, $delivery, $payment, $total);
$stmt->execute();

// Clear cart after order is placed
unset($_SESSION['cart']);

if ($payment == 'online') {
    // Redirect to PayMongo or payment gateway
    // Here we would integrate with PayMongo (This will be a separate task)
    // For now, we redirect to a success page
    header("Location: checkout.php");
    exit;
} else {
    // If it's COD, go to success page
    header("Location: success.php");
    exit;
}
?>

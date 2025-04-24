<?php
session_start();
include 'database.php';

$product_id = $_POST['product_id'] ?? null;
$quantity = intval($_POST['quantity'] ?? 1);

// validate product and get stock
$result = $conn->query("SELECT stock FROM products WHERE id = $product_id");
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

if ($quantity > $product['stock']) {
    echo "Not enough stock.";
    exit;
}

// initialize cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// add to cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// ✅ this is the redirect
header("Location: cart.php");
exit;

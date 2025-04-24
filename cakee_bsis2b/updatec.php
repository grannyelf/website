<?php
session_start();

$product_id = $_POST['product_id'] ?? null;
$quantity = intval($_POST['quantity'] ?? 1);

if ($product_id && $quantity > 0 && isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] = $quantity;
}

header("Location: cart.php");
exit;
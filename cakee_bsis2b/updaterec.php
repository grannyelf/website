<?php
session_start();

$product_id = $_POST['product_id'] ?? 0;

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header("Location: cart.php");
exit;
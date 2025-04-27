<?php
session_start();

$custom_text = $_POST['custom_index'] ?? '';

if (!empty($custom_text) && isset($_SESSION['cart_customized'])) {
    foreach ($_SESSION['cart_customized'] as $key => $item) {
        if ($item['custom_text'] === $custom_text) {
            unset($_SESSION['cart_customized'][$key]);
            break;
        }
    }
}

header("Location: cart.php");
exit;
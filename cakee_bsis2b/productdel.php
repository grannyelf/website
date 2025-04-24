<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'database.php';

if (isset($_POST['product_id'])) {
    $id = intval($_POST['product_id']);

    // (Optional) Delete image file if you want:
    // $imgResult = $conn->query("SELECT image FROM products WHERE id = $id");
    // $imgRow = $imgResult->fetch_assoc();
    // if ($imgRow && file_exists('images/' . $imgRow['image'])) {
    //     unlink('images/' . $imgRow['image']);
    // }

    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: productlist.php");
    exit;
}
?>

<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sdi", $name, $price, $id);
    $stmt->execute();
}

header("Location: productlist.php"); // or your actual file name
exit;
?>

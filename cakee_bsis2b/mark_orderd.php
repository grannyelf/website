<?php
include 'database.php';

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // ✅ Step 1: Mark order as done
    $stmt = $conn->prepare("UPDATE orders SET status='Done' WHERE id=?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    // ✅ Step 2: Get the items in this order
    $items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $items->bind_param("i", $orderId);
    $items->execute();
    $result = $items->get_result();

    // ✅ Step 3: Reduce stock per item
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantity = $row['quantity'];

        // Reduce stock in products table
        $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStock->bind_param("ii", $quantity, $productId);
        $updateStock->execute();
    }

    echo "<script>alert('Order marked as Done and stock updated!'); window.location.href='user_orders.php';</script>";
} else {
    echo "Invalid Order ID.";
}
?>
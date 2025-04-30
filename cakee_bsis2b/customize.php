<?php
session_start();
include 'database.php';

// Fetch available cakes
$stmt = $conn->prepare("SELECT * FROM products WHERE stock > 0");
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);
    $customText = trim($_POST['custom_text']);

    if ($productId && $customText) {
        // Get base price (optional; only if you want to show total later)
        $priceStmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $priceStmt->bind_param("i", $productId);
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result();
        $product = $priceResult->fetch_assoc();

        if ($product) {
            $customFee = 50;
            $quantity = 1;

            // Just store in session — not the database!
            $_SESSION['cart_customized'][] = [
                'product_id' => $productId,
                'custom_text' => $customText,
                'quantity' => $quantity,
                'custom_fee' => $customFee,
            ];

            header('Location: cart.php');
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Customize Your Cake - Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial; background: #fffaf5; padding: 2em; }
        form { background: white; padding: 2em; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h2 { text-align: center; color: #f78ca2; }
        label { display: block; margin-top: 1em; font-weight: bold; }
        select, input[type="text"] { width: 100%; padding: 0.8em; margin-top: 0.5em; border: 1px solid #ccc; border-radius: 5px; }
        button { margin-top: 2em; width: 100%; background: #f78ca2; color: white; padding: 1em; border: none; border-radius: 10px; font-size: 1em; cursor: pointer; }
    </style>
</head>
<body>
<h2>🎂 Customize Your Cake</h2>
<a href="user_dashb.php" style="text-align: center; display: block; margin-bottom: 10px;">Go Back?</a>

<form method="post" action="customize.php">
    <label>Select a Cake</label>
    <select name="product_id" required>
        <option value="">-- Choose a Cake --</option>
        <?php while ($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['name']) ?> (₱<?= number_format($row['price'], 2) ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <label>Custom Message</label>
    <input type="text" name="custom_text" maxlength="50" placeholder="e.g. Happy Birthday!" required>

    <p style="margin-top:1em; color: #666;">🎀 Note: Customization has a fixed ₱50 fee.</p>

    <button type="submit">Add to Cart</button>
</form>

</body>
</html>

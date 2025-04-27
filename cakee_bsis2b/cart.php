<?php
session_start();
include 'database.php';

$cart = $_SESSION['cart'] ?? [];
$products = [];

$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $cart[$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $products[] = $row;
        $total += $row['subtotal'];
    }
}

// Simulate distance in kilometers (later this will come from Google Maps API)
$simulatedDistance = 6; // in km

$baseFee = 30;
$additionalFee = max(0, ceil($simulatedDistance - 5) * 10); // ₱10/km after 5km
$deliveryFee = $baseFee + $additionalFee;

$grandTotal = $total + $deliveryFee;

// User address (assume user is logged in)
$userAddress = "";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT address FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userAddress = $stmt->get_result()->fetch_assoc()['address'] ?? '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart - Cakee Cakery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial; background: #fffaf5; padding: 2em; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 1em; border-bottom: 1px solid #eee; text-align: center; }
        th { background: #fdd1dd; }
        h2 { text-align: center; color: #f78ca2; }
        .total { text-align: right; margin-top: 1em; font-size: 1.2em; }
        .checkout-btn {
            display: block;
            margin: 2em auto;
            background: #f78ca2;
            color: white;
            border: none;
            padding: 1em 2em;
            border-radius: 10px;
            font-size: 1em;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>🛒 Your Cart</h2>

<?php
$cartCustomized = $_SESSION['cart_customized'] ?? [];
if (empty($products) && empty($cartCustomized)): ?>
    <p style="text-align:center;">Your cart is empty. <a href="user_dashb.php">Shop now</a>!</p>
<?php else: ?>
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>

        <!-- Show normal products -->
        <?php foreach ($products as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td>
                    <form action="updatec.php" method="post" style="display:inline-flex;">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" style="width: 60px;">
                        <button type="submit">Update</button>
                    </form>
                    <form action="updaterec.php" method="post" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:red;">❌</button>
                    </form>
                </td>
                <td>₱<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Show customized cakes -->
        <?php foreach ($cartCustomized as $item): 
            // Fetch cake info from DB
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $item['product_id']);
            $stmt->execute();
            $cake = $stmt->get_result()->fetch_assoc();

            if ($cake):
                $subtotal = ($cake['price'] + $item['custom_fee']) * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($cake['name']) ?> <br><small>🎀 Custom: "<?= htmlspecialchars($item['custom_text']) ?>"</small></td>
                <td>₱<?= number_format($cake['price'] + $item['custom_fee'], 2) ?> <br><small>(+₱50 custom fee)</small></td>
                <td>
                <form action="customrec.php" method="post" style="display:inline;">
                    <input type="hidden" name="custom_index" value="<?= htmlspecialchars($item['custom_text']) ?>">
                    <button type="submit" style="background:none;border:none;color:red;font-size:1.2em;">❌</button>
                </form>
                </td>
                <td>₱<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>

    </table>

    <!-- then the delivery fees and checkout part stay the same -->


    <div class="total">
        <p><strong>Cart Total: ₱<?= number_format($total, 2) ?></strong></p>
        <p>Delivery Address: <?= htmlspecialchars($userAddress) ?: "No address on file" ?></p>
        <p>Base Delivery Fee: ₱<?= number_format($baseFee, 2) ?></p>
        <p>Extra Distance Charge: ₱<?= number_format($additionalFee, 2) ?> (Distance: <?= $simulatedDistance ?> km)</p>
        <p><strong>Grand Total: ₱<?= number_format($grandTotal, 2) ?></strong></p>
    </div>

    <form action="checkout.php" method="post">
        <button type="submit" class="checkout-btn">Proceed to Checkout</button>
    </form>
    <form action="user_dashb.php" method="post">
        <button type="submit" class="checkout-btn">Shop More</button>
    </form>
<?php endif; ?>

</body>
</html>

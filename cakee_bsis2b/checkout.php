<?php
session_start();
include 'database.php';

$totalAmount = 0;
$cart = $_SESSION['cart'] ?? [];

// Calculate total amount
foreach ($cart as $product_id => $qty) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $totalAmount += $result['price'] * $qty;
}

$amountInCentavos = $totalAmount * 100;

// Simulate payment (true = success, false = failure)
$simulatePayment = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $status = 'Pending';

    // Insert order into `orders` table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $userId, $totalAmount, $status);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        $_SESSION['order_id'] = $orderId;

        // ✅ Insert each cart item into `order_items`
        foreach ($cart as $product_id => $qty) {
            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $itemStmt->bind_param("iii", $orderId, $product_id, $qty);
            $itemStmt->execute();
        }

        // Clear cart after successful order
        unset($_SESSION['cart']);

        // ✅ Redirect based on simulated payment result
        if ($simulatePayment) {
            echo "<script>setTimeout(() => { window.location.href = 'order_success.php'; }, 2500);</script>";
        } else {
            echo "<script>setTimeout(() => { window.location.href = 'checkout.php'; }, 2500);</script>";
        }
    } else {
        echo "<p style='color: red;'>Error while processing order: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processing Payment...</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fff8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 2em 3em;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #d94a76;
            margin-bottom: 0.5em;
        }
        p {
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Redirecting to PayMongo...</h2>
        <p>Please wait while we securely process your payment of <strong>₱<?= number_format($totalAmount, 2) ?></strong></p>
    </div>
</body>
</html>

<?php
// PayMongo API
$secretKey = 'sk_test_UZ5h6vUnkVeSHnWpF9tAMmpd';
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paymongo.com/v1/links",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        "data" => [
            "attributes" => [
                "amount" => $amountInCentavos,
                "description" => "Sweet Bliss Cake Order",
                "currency" => "PHP"
            ]
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Basic " . base64_encode($secretKey . ":"),
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($curl);
curl_close($curl);
$res = json_decode($response, true);

if (isset($res['data']['attributes']['checkout_url'])) {
    $checkoutUrl = $res['data']['attributes']['checkout_url'];
    echo "<script>setTimeout(() => { window.location.href = '$checkoutUrl'; }, 2500);</script>";
} else {
    echo "<p style='color: red;'>Error connecting to PayMongo.</p>";
}
?>

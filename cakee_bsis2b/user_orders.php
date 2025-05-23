<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'database.php';

// Fetch orders with user information including address
$stmt = $conn->prepare("
    SELECT 
        orders.id AS order_id,
        orders.total_amount,
        orders.status,
        orders.created_at,
        users.full_name,
        users.address,
        users.phone,
        products.name AS product_name,
        order_items.quantity
    FROM orders
    INNER JOIN users ON orders.user_id = users.id
    INNER JOIN order_items ON orders.id = order_items.order_id
    INNER JOIN products ON order_items.product_id = products.id
");
$stmt->execute();
$result = $stmt->get_result();

// Mark as Done
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done_id'])) {
    $orderIdToUpdate = intval($_POST['mark_done_id']);
    $adminId = $_SESSION['user_id']; // Admin ID

    // Update stocks
    $itemsStmt = $conn->prepare("
        SELECT product_id, quantity, custom_text 
        FROM order_items 
        WHERE order_id = ?
    ");
    $itemsStmt->bind_param("i", $orderIdToUpdate);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();

    while ($item = $itemsResult->fetch_assoc()) {
        $productId = $item['product_id'];
        $qty = $item['quantity'];
        $customText = $item['custom_text']; // Get custom text from the order items

        // Update stock
        $updateStockStmt = $conn->prepare("
            UPDATE products 
            SET stock = stock - ? 
            WHERE id = ? AND stock >= ?
        ");
        $updateStockStmt->bind_param("iii", $qty, $productId, $qty);
        $updateStockStmt->execute();
        
        // Insert custom text into logs
        $logStmt = $conn->prepare("INSERT INTO logs (order_id, action, admin_id, custom_text) VALUES (?, 'Done', ?, ?)");
        $logStmt->bind_param("iis", $orderIdToUpdate, $adminId, $customText); // Include custom text
        $logStmt->execute();
    }

    // Update order status
    $updateOrderStmt = $conn->prepare("UPDATE orders SET status = 'Done' WHERE id = ?");
    $updateOrderStmt->bind_param("i", $orderIdToUpdate);
    $updateOrderStmt->execute();

    header("Location: user_orders.php");
    exit;
}

// Mark as For Delivery
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_for_delivery_id'])) {
    $orderId = intval($_POST['mark_for_delivery_id']);
    $adminId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE orders SET status = 'Delivering' WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    $logStmt = $conn->prepare("INSERT INTO logs (order_id, action, admin_id) VALUES (?, 'Approved for Delivery', ?)");
    $logStmt->bind_param("ii", $orderId, $adminId);
    $logStmt->execute();

    header("Location: user_orders.php");
    exit;
}



// Cancel order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $orderIdToCancel = intval($_POST['cancel_order_id']);
    $adminId = $_SESSION['user_id']; // Admin ID

    // Update order status
    $cancelStmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
    $cancelStmt->bind_param("i", $orderIdToCancel);
    $cancelStmt->execute();

    // Insert into logs
    $logStmt = $conn->prepare("INSERT INTO logs (order_id, action, admin_id) VALUES (?, 'Cancelled', ?)");
    $logStmt->bind_param("ii", $orderIdToCancel, $adminId);
    $logStmt->execute();

    header("Location: user_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fffaf5;
            margin: 0;
            padding: 2em;
        }
        header {
            background: #f78ca2;
            color: white;
            padding: 1em;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 1em;
            text-decoration: none;
        }
        h1 {
            margin: 0;
        }
        table {
            width: 100%;
            margin-top: 2em;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1em;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background: #fdd1dd;
        }
        .status {
            font-weight: bold;
        }
        .status.Done {
            color: green;
        }
        .status.Cancelled {
            color: red;
        }
        .status.Pending {
            color: #f78ca2;
        }
    </style>
</head>
<body>

<header>
    <h1>All Orders</h1>
    <nav>
        <a href="admin_dashb.php">Go Back?</a>
    </nav>
</header>

<main>

<main>

<!-- Active Orders Section -->
<h2>Active Orders</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    mysqli_data_seek($result, 0);
    while ($row = $result->fetch_assoc()):
        if ($row['status'] !== 'Pending') continue;
    ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            <td class="status Pending"><?= $row['status'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="mark_for_delivery_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" style="padding:5px 10px; background:#FFA500; color:white; border:none; border-radius:5px;">Approve</button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirm('Cancel this order?');">
                    <input type="hidden" name="cancel_order_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" style="padding:5px 10px; background:#f44336; color:white; border:none; border-radius:5px;">Cancel</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>


<!-- Delivery Section -->
<h2 style="margin-top: 50px;">Delivery</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    mysqli_data_seek($result, 0);
    while ($row = $result->fetch_assoc()):
        if ($row['status'] !== 'Delivering') continue;
    ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            <td class="status ForDelivery"><?= $row['status'] ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="mark_done_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" style="padding:5px 10px; background:#4CAF50; color:white; border:none; border-radius:5px;">Mark as Done</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Completed/Cancelled Orders Section -->
<h2 style="margin-top: 50px;">Completed & Cancelled Orders</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    mysqli_data_seek($result, 0); // Reset again for second loop
    while ($row = $result->fetch_assoc()):
        if ($row['status'] !== 'Done' && $row['status'] !== 'Cancelled') continue;
    ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            <td class="status <?= $row['status'] ?>"><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?= $row['status'] === 'Done' ? '✅' : '❌' ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>


<h2 style="margin-top: 50px;">Order Action Logs</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Action</th>
        <th>Admin Name</th>
        <th>Time</th>
    </tr>
    <?php
    $logResult = $conn->query("
        SELECT logs.*, users.full_name AS admin_name 
        FROM logs
        INNER JOIN users ON logs.admin_id = users.id
        ORDER BY logs.action_time DESC
    ");
    while ($log = $logResult->fetch_assoc()):
    ?>
        <tr>
            <td><?= $log['order_id'] ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['admin_name']) ?></td>
            <td><?= date("M d, Y h:i A", strtotime($log['action_time'])) ?></td>
        </tr>
    <?php endwhile; ?>
</table>


<form method="post" action="purge_orders.php" onsubmit="return confirm('Are you sure you want to permanently delete all user orders?');" style="margin-bottom: 20px;">
    <button type="submit" style="background:#f44336; color:white; border:none; padding:10px 16px; border-radius:8px; font-weight:bold; margin-top: 20px;">
        🗑️ Purge All Orders
    </button>
</form>

</main>
        
</body>
</html>

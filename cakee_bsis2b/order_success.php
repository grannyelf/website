<?php
 session_start();
 include 'database.php';
 
 // Fetch the order from the session
 $orderId = $_SESSION['order_id'] ?? null;
 
 if ($orderId) {
     // Fetch the order details
     $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
     $stmt->bind_param("i", $orderId);
     $stmt->execute();
     $result = $stmt->get_result()->fetch_assoc();
 
     // Check if the order exists
     if ($result) {
         // If order status is still 'Pending', update it to 'Paid'
         if ($result['status'] == 'Pending') {
             $updateStmt = $conn->prepare("UPDATE orders SET status = 'Paid' WHERE id = ?");
             $updateStmt->bind_param("i", $orderId);
             $updateStmt->execute();
         }
     } else {
         echo "Order not found!";
         exit;
     }
 } else {
     echo "No order found!";
     exit;
 }
 ?>
 
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <title>Order Success</title>
     <link rel="stylesheet" href="style.css">
     <style>
         body {
             font-family: 'Segoe UI', sans-serif;
             background: #fff8f0;
             margin: 0;
             padding: 2em;
         }
 
         .success-box {
             background: white;
             padding: 2em 3em;
             border-radius: 16px;
             box-shadow: 0 10px 25px rgba(0,0,0,0.1);
             text-align: center;
             width: 60%;
             margin: 0 auto;
         }
 
         h2 {
             color: #d94a76;
             margin-bottom: 1em;
         }
 
         p {
             font-size: 1.1em;
         }
 
         ul {
             list-style-type: none;
             padding: 0;
         }
 
         ul li {
             margin: 0.5em 0;
         }
 
         .btn {
             background-color: #d94a76;
             color: white;
             padding: 1em;
             border-radius: 8px;
             text-decoration: none;
             display: inline-block;
             margin-top: 1em;
         }
 
         .btn:hover {
             background-color: #c13b64;
         }
     </style>
 </head>
 <body>
 
     <div class="success-box">
         <h2>Order Placed Successfully!</h2>
         <p>Your order has been successfully placed, and is now being processed. Below are the details:</p>
 
         <ul>
             <li><strong>Order ID:</strong> <?= $result['id'] ?></li>
             <li><strong>Amount:</strong> ₱<?= number_format($result['total_amount'], 2) ?></li>
             <li><strong>Delivery Method:</strong> <?= htmlspecialchars($result['delivery_method']) ?></li>
             <li><strong>Payment Method:</strong> <?= htmlspecialchars($result['payment_method']) ?></li>
             <li><strong>Address:</strong> <?= htmlspecialchars($result['address']) ?></li>
             <li><strong>Status:</strong> <?= $result['status'] ?></li>
         </ul>
 
         <a href="index.php" class="btn">Return to Home</a>
     </div>
 
 </body>
 </html>
<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = $_POST['message'];
    $reqId = $_POST['request_id'];
    $adminId = $_SESSION['user_id'];

    if (isset($_POST['close'])) {
        $conn->query("UPDATE help_requests SET status='Closed' WHERE id=$reqId");
    } else {
        $stmt = $conn->prepare("INSERT INTO help_replies (request_id, sender_role, sender_id, message) VALUES (?, 'admin', ?, ?)");
        $stmt->bind_param("iis", $reqId, $adminId, $msg);
        $stmt->execute();
    }

    header("Location: admin_help.php");
    exit;
}

$helpRequests = $conn->query("
    SELECT help_requests.*, users.full_name 
    FROM help_requests 
    JOIN users ON users.id = help_requests.user_id
    WHERE help_requests.status='Open'
    ORDER BY help_requests.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Help Panel</title>
    <style>
        body { font-family: Arial; background: #fffaf5; padding: 20px; }
        .ticket { background: #fff; border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
        .reply { margin-left: 20px; }
        .user { font-weight: bold; }
        textarea { width: 100%; height: 80px; margin-top: 10px; }
    </style>
</head>
<body>
<h2>Help Requests. <a href="admin_dashb.php">Go back?</a></h2>

<?php while ($r = $helpRequests->fetch_assoc()): ?>
    <div class="ticket">
        <p><strong><?= htmlspecialchars($r['full_name']) ?>:</strong> <?= htmlspecialchars($r['message']) ?></p>
        <p><em>Created at: <?= $r['created_at'] ?></em></p>

        <!-- Replies -->
        <?php
        $reqId = $r['id'];
        $replies = $conn->query("SELECT * FROM help_replies WHERE request_id=$reqId ORDER BY created_at ASC");
        while ($rep = $replies->fetch_assoc()):
        ?>
            <div class="reply">
                <span class="<?= $rep['sender_role'] == 'admin' ? 'user' : '' ?>">
                    <?= ucfirst($rep['sender_role']) ?>:
                </span> <?= htmlspecialchars($rep['message']) ?> <small>(<?= $rep['created_at'] ?>)</small>
            </div>
        <?php endwhile; ?>

        <!-- Reply form -->
        <form method="post">
            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
            <textarea name="message" required placeholder="Type a reply..."></textarea><br>
            <button type="submit">Send Reply</button>
            <button name="close" value="1" type="submit" style="background:#ccc;">Close Ticket</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="admin_help_archive.php">View Closed Tickets</a>
</body>
</html>

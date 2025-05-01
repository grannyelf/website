<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
include 'database.php';

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $reqId = $_POST['request_id'] ?? null;

    if ($reqId) {
        // Reply to existing request
        $stmt = $conn->prepare("INSERT INTO help_replies (request_id, sender_role, sender_id, message) VALUES (?, 'user', ?, ?)");
        $stmt->bind_param("iis", $reqId, $userId, $message);
    } else {
        // New help request
        $stmt = $conn->prepare("INSERT INTO help_requests (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $message);
    }

    $stmt->execute();
    header("Location: user_help.php");
    exit;
}

$requests = $conn->query("SELECT * FROM help_requests WHERE user_id=$userId AND status='Open' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Help Requests</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #fffaf5; }
        .ticket { margin-bottom: 20px; padding: 15px; background: white; border: 1px solid #ccc; border-radius: 10px; }
        textarea { width: 100%; height: 80px; margin-top: 10px; }
        .reply { margin-left: 20px; margin-top: 10px; }
        .admin { color: #d94a76; font-weight: bold; }
    </style>
</head>
<body>
<h2>Send a Help Request. <a href="user_dashb.php">Go back?</a></h2>
<form method="post">
    <textarea name="message" required placeholder="Describe your issue here..."></textarea><br>
    <button type="submit">Send Request</button>
</form>

<hr>

<h3>Open Requests</h3>
<?php while ($r = $requests->fetch_assoc()): ?>
    <div class="ticket">
        <p><strong>Request:</strong> <?= htmlspecialchars($r['message']) ?></p>
        <p><em>Posted at: <?= $r['created_at'] ?></em></p>

        <!-- Show replies -->
        <?php
        $reqId = $r['id'];
        $replies = $conn->query("SELECT * FROM help_replies WHERE request_id=$reqId ORDER BY created_at ASC");
        while ($rep = $replies->fetch_assoc()):
        ?>
            <div class="reply">
                <span class="<?= $rep['sender_role'] == 'admin' ? 'admin' : '' ?>">
                    <?= ucfirst($rep['sender_role']) ?>:
                </span> <?= htmlspecialchars($rep['message']) ?> <small>(<?= $rep['created_at'] ?>)</small>
            </div>
        <?php endwhile; ?>

        <!-- Reply form -->
        <form method="post">
            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
            <textarea name="message" required placeholder="Reply here..."></textarea><br>
            <button type="submit">Reply</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="user_help_archive.php">View Closed Tickets</a>
</body>
</html>

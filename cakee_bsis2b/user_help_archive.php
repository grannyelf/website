<?php
session_start();
include 'database.php';

$userId = $_SESSION['user_id'];
$closedRequests = $conn->query("SELECT * FROM help_requests WHERE user_id=$userId AND status='Closed' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Closed Help Requests</title>
</head>
<body>
<h2>Closed Help Requests</h2>
<a href="user_help.php">← Back to Active</a>

<?php while ($r = $closedRequests->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
        <p><strong>Issue:</strong> <?= htmlspecialchars($r['message']) ?></p>
        <p><em>Created: <?= $r['created_at'] ?></em></p>
        <p><em>Status: <?= $r['status'] ?></em></p>

        <?php
        $reqId = $r['id'];
        $replies = $conn->query("SELECT * FROM help_replies WHERE request_id=$reqId");
        while ($rep = $replies->fetch_assoc()):
        ?>
            <div style="margin-left:20px;">
                <strong><?= ucfirst($rep['sender_role']) ?>:</strong> <?= htmlspecialchars($rep['message']) ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php endwhile; ?>
</body>
</html>

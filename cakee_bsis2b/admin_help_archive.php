<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$closedRequests = $conn->query("
    SELECT help_requests.*, users.full_name 
    FROM help_requests 
    JOIN users ON users.id = help_requests.user_id
    WHERE help_requests.status='Closed'
    ORDER BY help_requests.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Archived Help Tickets</title>
</head>
<body>
<h2>Archived Help Requests</h2>
<a href="admin_help.php">← Back to Active</a>

<?php while ($r = $closedRequests->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
        <p><strong><?= $r['full_name'] ?>:</strong> <?= htmlspecialchars($r['message']) ?></p>
        <p><em>Created: <?= $r['created_at'] ?></em></p>

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

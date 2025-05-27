<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: /login-page");
    exit;
}
?>

<h2>Your Service Requests</h2>

<table border="1">
    <tr><th>Category</th><th>Description</th><th>Status</th><th>Date</th></tr>
    <?php foreach ($requests as $req): ?>
        <tr>
            <td><?= htmlspecialchars($req['category']) ?></td>
            <td><?= htmlspecialchars($req['description']) ?></td>
            <td><?= htmlspecialchars($req['status']) ?></td>
            <td><?= htmlspecialchars($req['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="/views/citizen/submit_request.php">Submit New Request</a> |
<a href="/controllers/AuthController.php?action=logout">Logout</a>


<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login-page");
    exit;
}
?>

<h2>Request Statistics</h2>

<table border="1">
    <tr><th>Category</th><th>Total Requests</th></tr>
    <?php foreach ($stats as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['count'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="/controllers/AuthController.php?action=logout">Logout</a>

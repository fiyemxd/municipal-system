<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: /login-page");
    exit;
}

?>

<h2>Assigned Service Requests</h2>

<form method="POST" action="/controllers/EmployeeController.php?action=update">
    <table border="1">
        <tr><th>Select</th><th>Category</th><th>Description</th><th>Status</th><th>Location</th></tr>
        <?php foreach ($requests as $req): ?>
        <tr>
            <td><input type="radio" name="id" value="<?= $req['id'] ?>" required></td>
            <td><?= htmlspecialchars($req['category']) ?></td>
            <td><?= htmlspecialchars($req['description']) ?></td>
            <td><?= htmlspecialchars($req['status']) ?></td>
            <td>
                <a href="https://www.google.com/maps?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank">
                    View Map
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <label>Update Status:</label>
    <select name="status" required>
        <option value="In Progress">In Progress</option>
        <option value="Resolved">Resolved</option>
    </select>
    <button type="submit">Update</button>
</form>

<a href="/controllers/AuthController.php?action=logout">Logout</a>

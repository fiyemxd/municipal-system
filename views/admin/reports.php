<?php
session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$stats = Request::getStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Service Request Statistics</h3>
  <table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
      <tr>
        <th>Category</th>
        <th>Total Requests</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stats as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= $row['count'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

<?php
session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: ../login.php");
    exit;
}
$requests = Request::getByUser($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Citizen Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Your Service Requests</h3>
  <table class="table table-bordered table-striped mt-3">
    <thead>
      <tr>
        <th>Category</th>
        <th>Description</th>
        <th>Status</th>
        <th>Location</th>
        <th>Media</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td><?= htmlspecialchars($r['description']) ?></td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td><a href="https://maps.google.com/?q=<?= $r['latitude'] ?>,<?= $r['longitude'] ?>" target="_blank">Map</a></td>
          <td><?php if ($r['media_path']) echo "<a href='{$r['media_path']}' target='_blank'>View</a>"; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

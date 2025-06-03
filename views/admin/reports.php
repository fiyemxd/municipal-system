<?php
session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$requests = Request::getAllRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Service Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>All Submitted Service Requests</h3>
  <table class="table table-bordered table-striped mt-4 align-middle">
    <thead class="table-dark">
      <tr>
        <th>Category</th>
        <th>Status</th>
        <th>User</th>
        <th>Created At</th>
        <th>Media</th>
        <th>Location</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $req): ?>
        <tr>
          <td><?= htmlspecialchars($req['category']) ?></td>
          <td>
            <?php
              $badge = match($req['status']) {
                'Pending' => 'badge bg-warning text-dark',
                'In Progress' => 'badge bg-info',
                'Resolved' => 'badge bg-success',
                default => 'badge bg-secondary'
              };
              echo "<span class=\"$badge\">{$req['status']}</span>";
            ?>
          </td>
          <td><?= htmlspecialchars($req['username']) ?></td>
          <td><?= htmlspecialchars($req['created_at']) ?></td>
          <td>
            <?php if (!empty($req['media_path'])): ?>
              <a href="<?= $req['media_path'] ?>" target="_blank">
                <img src="<?= $req['media_path'] ?>" style="width: 50px;">
              </a>
            <?php else: ?>
              <span class="text-muted">None</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="https://maps.google.com/?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Map</a>
          </td>
          <td><?= nl2br(htmlspecialchars($req['description'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

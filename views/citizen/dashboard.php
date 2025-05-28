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
  <title>Your Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Your Submitted Requests</h3>
  <table class="table table-bordered table-striped align-middle mt-3">
    <thead class="table-dark">
      <tr>
        <th>Category</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Media</th>
        <th>Location</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $index => $req): ?>
        <tr>
          <td><?= htmlspecialchars($req['category']) ?></td>
          <td>
            <?php
              $badgeClass = match($req['status']) {
                'Pending' => 'badge bg-warning text-dark',
                'In Progress' => 'badge bg-info',
                'Resolved' => 'badge bg-success',
                default => 'badge bg-secondary'
              };
              echo "<span class=\"$badgeClass\">{$req['status']}</span>";
            ?>
          </td>
          <td><?= htmlspecialchars($req['created_at']) ?></td>
          <td>
            <?php if ($req['media_path']): ?>
              <a href="<?= $req['media_path'] ?>" target="_blank">
                <img src="<?= $req['media_path'] ?>" style="width: 50px;">
              </a>
            <?php else: ?>
              <span class="text-muted">None</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="https://maps.google.com/?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">Map</a>
          </td>
          <td>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#descModal<?= $index ?>">Details</button>
          </td>
        </tr>

        <!-- Modal -->
        <div class="modal fade" id="descModal<?= $index ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Description</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <?= nl2br(htmlspecialchars($req['description'])) ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

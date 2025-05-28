<?php
session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit;
}

$requests = Request::getResolved();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Pending Service Requests</h3>
  <table class="table table-bordered table-striped mt-3 align-middle">
    <thead class="table-dark">
      <tr>
        <th>Category</th>
        <th>Status</th>
        <th>Created By</th>
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
          <td><?= htmlspecialchars($req['username']) ?? '-' ?></td>
          <td><?= htmlspecialchars($req['created_at']) ?></td>
          <td>
            <?php if (!empty($req['media_path'])): ?>
              <a href="<?= $req['media_path'] ?>" target="_blank">
                <img src="<?= $req['media_path'] ?>" alt="media" style="width: 50px;">
              </a>
            <?php else: ?>
              <span class="text-muted">None</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="https://maps.google.com/?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">Map</a>
          </td>
          <td>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $index ?>">Details</button>
          </td>
        </tr>

        <!-- Modal -->
        <div class="modal fade" id="detailModal<?= $index ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <form method="POST" action="../../controllers/EmployeeController.php?action=update_status_inline">
                <div class="modal-header bg-dark text-white">
                  <h5 class="modal-title">Request Details</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= $req['id'] ?>">
                  <input type="hidden" name="from" value="resolved_requests.php">

                  <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea class="form-control" disabled><?= htmlspecialchars($req['description']) ?></textarea>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Status:</label>
                    <select name="status" class="form-select">
                      <option value="Pending" <?= $req['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="In Progress" <?= $req['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="Resolved" <?= $req['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Media Preview:</label><br>
                    <?php if ($req['media_path']): ?>
                      <img src="<?= $req['media_path'] ?>" style="max-width: 100%; height: auto;">
                    <?php else: ?>
                      <span class="text-muted">No media available.</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Update Status</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

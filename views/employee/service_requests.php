<?php
function base_url($path = '') {
    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}

session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit;
}

$statusFilter = $_GET['status'] ?? 'pending';

$pageTitleMap = [
    'pending' => 'Pending Requests',
    'in_progress' => 'In Progress Requests',
    'resolved' => 'Resolved Requests'
];
$pageTitle = $pageTitleMap[$statusFilter] ?? 'Service Requests';

$requests = match ($statusFilter) {
    default => Request::getPending(),
    'resolved' => Request::getResolved(),
    'in_progress' => Request::getInProgress()
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3><?= $pageTitle ?></h3>

  <div class="mb-3">
    <a href="?status=pending" class="btn btn-outline-secondary btn-sm <?= $statusFilter == 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="?status=in_progress" class="btn btn-outline-secondary btn-sm <?= $statusFilter == 'in_progress' ? 'active' : '' ?>">In Progress</a>
    <a href="?status=resolved" class="btn btn-outline-secondary btn-sm <?= $statusFilter == 'resolved' ? 'active' : '' ?>">Resolved</a>
  </div>

  <table class="table table-bordered table-striped align-middle">
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
      <?php foreach ($requests as $req): ?>
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
              <a href="<?= base_url($req['media_path']) ?>" target="_blank">
                <img src="<?= base_url($req['media_path']) ?>" alt="media" style="width: 50px;">
              </a>
            <?php else: ?>
              <span class="text-muted">None</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="https://maps.google.com/?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">Map</a>
          </td>
          <td>
            <button class="btn btn-info btn-sm" onclick="showRequestDetails(<?= $req['id'] ?>)">Details</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Modal -->
  <div class="modal fade" id="requestDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title">Request Detail</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="requestDetailContent">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function getStatusBadgeClass(status) {
    switch(status) {
      case 'Pending': return 'bg-warning text-dark';
      case 'In Progress': return 'bg-info';
      case 'Resolved': return 'bg-success';
      default: return 'bg-secondary';
    }
  }

  function showRequestDetails(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('requestDetailModal'));
    const content = document.getElementById('requestDetailContent');

    content.innerHTML = `<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

    modal.show();

    fetch(`../citizen/get_request_detail.php?id=${requestId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const req = data.request;
          content.innerHTML = `
            <form method="POST" action="../../controllers/RequestController.php?action=update_status_inline">
              <input type="hidden" name="id" value="${req.id}">
              <input type="hidden" name="from" value="service_requests.php">

              <div class="row">
                <div class="col-md-6">
                  <h6>Category</h6>
                  <p class="text-muted">${req.category}</p>

                  <h6>Status</h6>
                  <select name="status" class="form-select form-select-sm mb-3">
                    <option value="Pending" ${req.status === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="In Progress" ${req.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                    <option value="Resolved" ${req.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                  </select>

                  <h6>Created</h6>
                  <p class="text-muted">${req.created_at}</p>

                  <h6>Description</h6>
                  <div class="bg-light p-2 rounded small">${req.description.replace(/\n/g, '<br>')}</div>
                </div>
                <div class="col-md-6">
                  ${req.media_path ? `
                    <h6>Media</h6>
                    <a href="/${req.media_path}" target="_blank">
                      <img src="/${req.media_path}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    </a>
                  ` : ''}
                  <h6>Location</h6>
                  <div class="ratio ratio-4x3">
                    <iframe 
                      src="https://maps.google.com/maps?q=${req.latitude},${req.longitude}&hl=tr&z=16&output=embed"
                      frameborder="0" style="border:0;" allowfullscreen>
                    </iframe>
                  </div>
                </div>
              </div>

              <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Update Status</button>
              </div>
            </form>
          `;

        } else {
          content.innerHTML = `<div class="alert alert-danger"><h6>Error</h6><p>${data.message || 'Detaylar yüklenemedi.'}</p></div>`;
        }
      })
      .catch(error => {
        content.innerHTML = `<div class="alert alert-danger"><h6>Connection Error</h6><p>Detaylar alınırken hata oluştu.</p></div>`;
      });
  }
</script>
</body>
</html>

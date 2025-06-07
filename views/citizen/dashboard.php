<?php
// Diğer kodların üstüne ekleyin
function base_url($path = '') {
    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');
require_once realpath(__DIR__ . '/../../models/Notification.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: ../login.php");
    exit;
}

$unreadNotifications = [];
$allRecentNotifications = [];
$requests = [];
$error_message = null;

try {
    $requests = Request::getByUser($_SESSION['user_id']);
    $unreadNotifications = Notification::getUnread($_SESSION['user_id']);
    $allRecentNotifications = Notification::getRecent($_SESSION['user_id'], 10);

    // 🔽 Debug çıktısı burada olacak
    echo "<!-- Unread Notifications: " . print_r($unreadNotifications, true) . " -->";
    echo "<!-- All Notifications: " . print_r($allRecentNotifications, true) . " -->";

} catch (Exception $e) {
    $error_message = "Veriler yüklenirken hata oluştu: " . $e->getMessage();
}

// === CLEAR NOTIFICATIONS İŞLEMİ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_notifications') {
    $result = Notification::markAllAsRead($_SESSION['user_id']);
    if ($result) {
        // Başarılı işlem sonrası sayfayı yenile
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Bildirimler okundu olarak işaretlenemedi.";
    }
}

// === TEK BİLDİRİMİ OKUNDU YAPMA İŞLEMİ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_single_read') {
    $notificationId = $_POST['notification_id'] ?? null;
    if ($notificationId) {
        $result = Notification::markAsRead($notificationId, $_SESSION['user_id']);
        echo json_encode(['success' => $result]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

$requests = Request::getByUser($_SESSION['user_id']);
$unreadNotifications = Notification::getUnread($_SESSION['user_id']);
$allRecentNotifications = Notification::getRecent($_SESSION['user_id'], 10); // Son 10 bildirim
$unreadCount = count($unreadNotifications);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
  /* Bildirim kutularının metin alanları düzgün satır kayması yapsın */
  .notification-item p {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: unset !important;
    margin-bottom: 5px;
  }

  /* Bildirim kutuları daha ferah ve okunaklı */
  .notification-item {
    padding: 12px 18px;
    line-height: 1.5;
    border-radius: 8px;
    transition: background-color 0.2s ease;
  }

  .notification-item:hover {
    background-color: #f0f8ff; /* Hover efekti */
  }

  /* Zaten var ama varsa güçlendirelim */
  #allNotificationsModal .modal-dialog {
    max-width: 800px;
  }

  /* Description text styling */
  .description-text {
    line-height: 1.4;
    max-height: 3.6em; /* ~3 satır */
    overflow: hidden;
  }

  /* Büyüteç butonu hover efekti */
  .btn:has(🔍):hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
  }
</style>

</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>

<div class="container mt-4">

  <?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
  <?php endif; ?>

  <!-- Bildirim kutusu -->
  <div class="d-flex justify-content-between align-items-center">
    <h3>Your Submitted Requests</h3>
    
    <!-- Bildirim dropdown - her zaman görünür -->
    <div class="dropdown">
      <button class="btn <?= $unreadCount > 0 ? 'btn-danger' : 'btn-outline-secondary' ?> dropdown-toggle position-relative" 
              type="button" data-bs-toggle="dropdown">
        🔔 Notifications
        <?php if ($unreadCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $unreadCount ?>
          </span>
        <?php endif; ?>
      </button>
      
      <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <?php if (count($allRecentNotifications) > 0): ?>
          <?php foreach ($allRecentNotifications as $note): ?>
            <li class="<?= $note['is_read'] == 0 ? 'bg-light' : '' ?>">
              <div class="dropdown-item p-3 notification-item" 
                data-request-id="<?= $note['request_id'] ?? '' ?>"
                data-notification-id="<?= $note['id'] ?? '' ?>"
                style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <p class="mb-1 <?= $note['is_read'] == 0 ? 'fw-bold' : 'text-muted' ?>">
                      <?= htmlspecialchars($note['message']) ?>
                    </p>
                    <small class="text-muted">
                      <?= date('M j, H:i', strtotime($note['created_at'])) ?>
                    </small>
                  </div>
                  <?php if ($note['is_read'] == 0): ?>
                    <span class="badge bg-primary ms-2">Yeni</span>
                  <?php endif; ?>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
          
          <li><hr class="dropdown-divider"></li>
          
          <!-- İşlem butonları -->
          <li class="p-2">
            <div class="d-flex justify-content-between">
              <?php if ($unreadCount > 0): ?>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="action" value="clear_notifications">
                  <button type="submit" class="btn btn-sm btn-primary">Hepsini Okundu</button>
                </form>
              <?php endif; ?>
              <button class="btn btn-sm btn-outline-primary" onclick="showAllNotifications()">
                Tümünü Gör
              </button>
            </div>
          </li>
          
        <?php else: ?>
          <li class="dropdown-item text-center text-muted py-3">
            <div>
              🔕<br>
              <small>Henüz bildirim yok</small>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

<table class="table table-bordered table-striped align-middle mt-3">
    <thead class="table-dark">
      <tr>
        <th>Category</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Media</th>
        <th>Location</th>
        <th style="min-width: 250px;">Details</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $index => $req): ?>
        <tr>
          <td class="align-middle"><?= htmlspecialchars($req['category']) ?></td>
          <td class="align-middle">
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
          <td class="align-middle"><?= htmlspecialchars($req['created_at']) ?></td>
          <td class="align-middle">
              <?php if ($req['media_path']): ?>
                  <a href="<?= base_url($req['media_path']) ?>" target="_blank">
                      <img src="<?= base_url($req['media_path']) ?>" style="width: 50px;">
                  </a>
              <?php else: ?>
                  <span class="text-muted">None</span>
              <?php endif; ?>
          </td>
          <td class="align-middle">
            <a href="https://maps.google.com/?q=<?= $req['latitude'] ?>,<?= $req['longitude'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">📍 Map</a>
          </td>
          <td class="align-middle">
            <div class="d-flex align-items-start justify-content-between">
              <div class="description-text flex-grow-1 me-2" style="font-size: 1em; line-height: 1.4;">
                <?php 
                  $description = htmlspecialchars($req['description']);
                  echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                ?>
              </div>
              <button class="btn btn-outline-primary btn-sm flex-shrink-0" 
                      onclick="showRequestDetails(<?= $req['id'] ?? $req['request_id'] ?? $index ?>)" 
                      title="View Full Details">
                🔍 Details
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
</table>

<!-- Request Detay Modal -->
<div class="modal fade" id="requestDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Request Details</h5>
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

<!-- Tüm Bildirimler Modal -->
<div class="modal fade" id="allNotificationsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">All Notifications</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="allNotificationsContent">
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

// Tek bildirimi okundu yapma fonksiyonu
function markNotificationAsRead(notificationId) {
  if (!notificationId) return Promise.resolve(false);

  return fetch(window.location.href, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=mark_single_read&notification_id=${notificationId}`
  })
  .then(response => response.json())
  .then(data => data.success)
  .catch(() => false);
}

// Request detayını göster
function showRequestDetails(requestId, notificationId = null) {
  if (!requestId) {
    alert('Bu bildirim için detay bulunamadı.');
    return;
  }

  // Eğer notification ID varsa, önce bildirimi okundu yap
  if (notificationId) {
    markNotificationAsRead(notificationId).then(() => {
      // Bildirim okundu yapıldıktan sonra sayfayı yenile (bildirim sayacını güncellemek için)
      setTimeout(() => {
        location.reload();
      }, 500);
    });
  }

  // 🔽 "Tümünü gör" modalını kapat
  const allNotifModal = bootstrap.Modal.getInstance(document.getElementById('allNotificationsModal'));
  if (allNotifModal) {
    allNotifModal.hide();
  }

  const modal = new bootstrap.Modal(document.getElementById('requestDetailModal'));
  const content = document.getElementById('requestDetailContent');

  // Loading göster
  content.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  `;

  modal.show();

  fetch(`./get_request_detail.php?id=${requestId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const req = data.request;
        content.innerHTML = `
          <div class="row">
            <div class="col-md-6">
              <h6>Category</h6>
              <p class="text-muted">${req.category}</p>

              <h6>Status</h6>
              <span class="badge ${getStatusBadgeClass(req.status)}">${req.status}</span>

              <h6 class="mt-3">Created</h6>
              <p class="text-muted">${req.created_at}</p>

              <h6>Description</h6>
              <div class="bg-light p-3 rounded">
                ${req.description.replace(/\n/g, '<br>')}
              </div>
            </div>
            <div class="col-md-6">
              ${req.media_path ? `
                <h6>Media</h6>
                <a href="/${req.media_path}" target="_blank">
                  <img src="/${req.media_path}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                </a>
              ` : ''}

              <h6>Location</h6>
              <div class="ratio ratio-4x3 mb-2">
                <iframe 
                  src="https://maps.google.com/maps?q=${req.latitude},${req.longitude}&hl=tr&z=16&output=embed"
                  frameborder="0" 
                  style="border:0;" 
                  allowfullscreen>
                </iframe>
              </div>
            </div>
          </div>
        `;

      } else {
        content.innerHTML = `
          <div class="alert alert-danger">
            <h6>Error</h6>
            <p>${data.message || 'Request details could not be loaded.'}</p>
          </div>
        `;
      }
    })
    .catch(error => {
      content.innerHTML = `
        <div class="alert alert-danger">
          <h6>Connection Error</h6>
          <p>Could not load request details. Please try again.</p>
        </div>
      `;
    });
}


// Tüm bildirimleri göster
function showAllNotifications() {
  const modal = new bootstrap.Modal(document.getElementById('allNotificationsModal'));
  const content = document.getElementById('allNotificationsContent');

  content.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  `;

  modal.show();

  fetch('./get_all_notifications.php')
    .then(response => response.json())
    .then(data => {
      if (data.success && data.notifications.length > 0) {
        let html = '';
        data.notifications.forEach(note => {
          html += `
            <div class="notification-item border-bottom py-3 ${note.is_read == 0 ? 'bg-light' : ''}" 
                 style="cursor: pointer;" 
                 onclick="showRequestDetails(${note.request_id || null}, ${note.id || null});">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <p class="mb-1 ${note.is_read == 0 ? 'fw-bold' : 'text-muted'}" title="${note.message}">
                    ${note.message}
                  </p>
                  <small class="text-muted">
                    ${new Date(note.created_at).toLocaleString()}
                  </small>
                </div>
                ${note.is_read == 0 ? '<span class="badge bg-primary">Yeni</span>' : ''}
              </div>
            </div>
          `;
        });
        content.innerHTML = html;
      } else {
        content.innerHTML = '<div class="text-center text-muted py-4">Bildirim bulunamadı.</div>';
      }
    })
    .catch(error => {
      content.innerHTML = '<div class="alert alert-danger py-4">Bildirimler yüklenemedi.</div>';
    });
}

// Status badge class helper
function getStatusBadgeClass(status) {
  switch(status) {
    case 'Pending': return 'bg-warning text-dark';
    case 'In Progress': return 'bg-info';
    case 'Resolved': return 'bg-success';
    default: return 'bg-secondary';
  }
}


// Bildirim öğelerine tıklanınca request detaylarını getir
document.querySelectorAll('.notification-item').forEach(item => {
  item.addEventListener('click', function () {
    const requestId = this.getAttribute('data-request-id');
    const notificationId = this.getAttribute('data-notification-id');
    showRequestDetails(requestId, notificationId);
  });
});


</script>

</body>
</html>
<?php
session_start();
require_once realpath(__DIR__ . '/../../models/Request.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$requests = Request::getAllWithLocation();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Request Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #map { height: 80vh; }
  </style>
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Service Requests Map</h3>
  <div id="map" class="mt-3 rounded shadow"></div>
</div>

<script>
  var map = L.map('map').setView([39.92, 32.85], 6); // Türkiye merkezli

  // Tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Marker colors by status
  function getMarkerColor(status) {
    switch (status) {
      case 'Pending': return 'orange';
      case 'In Progress': return 'blue';
      case 'Resolved': return 'green';
      default: return 'gray';
    }
  }

  <?php foreach ($requests as $r): ?>
    L.circleMarker([<?= $r['latitude'] ?>, <?= $r['longitude'] ?>], {
      color: getMarkerColor('<?= $r['status'] ?>'),
      radius: 8,
      fillOpacity: 0.8
    }).addTo(map)
    .bindPopup(`<strong>Category:</strong> <?= htmlspecialchars($r['category']) ?><br>
                <strong>User:</strong> <?= htmlspecialchars($r['username']) ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($r['status']) ?><br>
                <strong>Created:</strong> <?= $r['created_at'] ?><br>
                <strong>Description:</strong><br><?= nl2br(htmlspecialchars($r['description'])) ?>`);
  <?php endforeach; ?>
</script>
</body>
</html>

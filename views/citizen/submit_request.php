<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Request</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    #map { height: 300px; }
    #customCategoryDiv { display: none; }
    .leaflet-bottom.leaflet-right {
      margin-bottom: 40px;
    }
  </style>
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Submit a New Service Request</h3>
  <form method="POST" action="../../controllers/CitizenController.php?action=submit" enctype="multipart/form-data" class="border p-4 rounded bg-light shadow">

    <div class="mb-3">
      <label class="form-label">Issue Category:</label>
      <select name="category" class="form-select" id="categorySelect" required>
        <option value="Pothole">Pothole</option>
        <option value="Streetlight Outage">Streetlight Outage</option>
        <option value="Waste Collection">Waste Collection</option>
        <option value="Graffiti">Graffiti</option>
        <option value="Water Leakage">Water Leakage</option>
        <option value="Electricity Outage">Electricity Outage</option>
        <option value="Stray Animal">Stray Animal</option>
        <option value="Noise Complaint">Noise Complaint</option>
        <option value="Fallen Tree">Fallen Tree</option>
        <option value="Illegal Parking">Illegal Parking</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <div class="mb-3" id="customCategoryDiv">
      <label class="form-label">Specify Your Issue:</label>
      <input type="text" name="custom_category" class="form-control" placeholder="E.g. Broken Bench">
    </div>

    <div class="mb-3">
      <label class="form-label">Description:</label>
      <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Attach Media (optional):</label>
      <input type="file" name="media" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Select Location:</label>
      <div id="map" class="rounded border"></div>
      <button type="button" id="locateBtn" class="btn btn-outline-secondary btn-sm mt-2">📍 Use My Location</button>
    </div>

    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lon">

    <button type="submit" class="btn btn-primary mt-3">Submit Request</button>
  </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  // 1. Kategori seçimine göre diğer alanı göster
  document.getElementById('categorySelect').addEventListener('change', function () {
    const otherField = document.getElementById('customCategoryDiv');
    otherField.style.display = (this.value === 'Other') ? 'block' : 'none';
  });

  // 2. Leaflet harita ayarı
  const map = L.map('map').setView([38.4420, 27.1020], 12); // İzmir
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  let marker;

  function setPin(lat, lon) {
    if (marker) {
      marker.setLatLng([lat, lon]);
    } else {
      marker = L.marker([lat, lon], {draggable: true}).addTo(map);
      marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('lat').value = pos.lat;
        document.getElementById('lon').value = pos.lng;
      });
    }
    document.getElementById('lat').value = lat;
    document.getElementById('lon').value = lon;
    map.setView([lat, lon], 14);
  }

  // 3. Haritaya tıklama → pin koy
  map.on('click', function(e) {
    const {lat, lng} = e.latlng;
    setPin(lat, lng);
  });

  // 4. “Use my location” butonu
  document.getElementById('locateBtn').addEventListener('click', function () {
    navigator.geolocation.getCurrentPosition(function(pos) {
      setPin(pos.coords.latitude, pos.coords.longitude);
    }, function() {
      alert("Konum alınamadı. Lütfen tarayıcınızın konum erişimine izin verin.");
    });
  });
</script>
</body>
</html>

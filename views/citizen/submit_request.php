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
</head>
<body>
<?php include_once realpath(__DIR__ . '/../partials/navbar.php'); ?>
<div class="container mt-4">
  <h3>Submit a Service Request</h3>
  <form method="POST" action="../../controllers/CitizenController.php?action=submit" enctype="multipart/form-data" class="border p-3 rounded bg-light">
    <div class="mb-3">
      <label class="form-label">Issue Category:</label>
      <select name="category" class="form-select" required>
        <option value="Pothole">Pothole</option>
        <option value="Street light">Streetlight</option>
        <option value="Waste Collection">Waste Collection</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Description:</label>
      <textarea name="description" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Attach Media:</label>
      <input type="file" name="media" class="form-control">
    </div>
    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lon">
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>
<script>
navigator.geolocation.getCurrentPosition(function(pos) {
    document.getElementById('lat').value = pos.coords.latitude;
    document.getElementById('lon').value = pos.coords.longitude;
});
</script>
</body>
</html>

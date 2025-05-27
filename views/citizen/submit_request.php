<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: /login-page");
    exit;
}
?>

<h2>Submit a Service Request</h2>

<form method="POST" action="../../controllers/CitizenController.php?action=submit" enctype="multipart/form-data">
    <label>Issue Category:</label>
    <select name="category" required>
        <option value="pothole">Pothole</option>
        <option value="light">Streetlight</option>
        <option value="waste">Waste Collection</option>
    </select><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" cols="40" required></textarea><br>

    <label>Attach Media:</label>
    <input type="file" name="media"><br>

    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lon">

    <input type="submit" value="Submit Request">
</form>

<script>
navigator.geolocation.getCurrentPosition(function(position) {
    document.getElementById('lat').value = position.coords.latitude;
    document.getElementById('lon').value = position.coords.longitude;
});
</script>

<a href="/views/citizen/dashboard.php">Go to Dashboard</a> |
<a href="/controllers/AuthController.php?action=logout">Logout</a>

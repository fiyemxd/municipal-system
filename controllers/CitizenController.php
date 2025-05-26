<?php
require_once 'models/Request.php';

function submitRequest() {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $userId = 1; // Demo amaçlı sabit
    $mediaPath = '';

    if ($_FILES['media']['error'] === 0) {
        $target = 'uploads/' . basename($_FILES['media']['name']);
        move_uploaded_file($_FILES['media']['tmp_name'], $target);
        $mediaPath = $target;
    }

    Request::submit($userId, $category, $description, $mediaPath, $latitude, $longitude);
    echo "Request submitted successfully.";
}

function showCitizenDashboard() {
    $userId = 1;
    $requests = Request::getByUser($userId);
    include 'views/citizen/dashboard.php';
}

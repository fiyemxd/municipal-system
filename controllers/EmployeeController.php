<?php
session_start();
require_once '../models/Request.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../views/login.php");
    exit;
}

// === DURUM GÜNCELLE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $resolvePhoto = '';

    if (isset($_FILES['resolve_photo']) && $_FILES['resolve_photo']['error'] === 0) {
        $target = '../uploads/' . basename($_FILES['resolve_photo']['name']);
        move_uploaded_file($_FILES['resolve_photo']['tmp_name'], $target);
        $resolvePhoto = $target;
    }

    Request::updateStatus($id, $status, $resolvePhoto);
    header("Location: ../views/employee/request_list.php");
    exit;
}

// === PANEL İÇİN VERİ ÇEK ===
function showEmployeeRequests() {
    $requests = Request::getAssigned();
    include '../views/employee/request_list.php';
}
if (isset($_GET['action']) && $_GET['action'] === 'show') {
    showEmployeeRequests();
}

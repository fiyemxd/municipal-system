<?php
session_start();
require_once '../models/Request.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.php");
    exit;
}

// === ADMIN RAPORLAMA ===
function showReports() {
    $stats = Request::getStatistics(); // Kategoriye göre COUNT()
    include '../views/admin/reports.php';
}

// (İsteğe bağlı) Harita için tüm verileri JSON olarak döndürmek istersen:
if (isset($_GET['action']) && $_GET['action'] === 'mapdata') {
    $all = Request::getAll();
    header('Content-Type: application/json');
    echo json_encode($all);
    exit;
}

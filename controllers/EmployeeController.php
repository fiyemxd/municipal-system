<?php
session_start();
require_once '../models/Request.php';
require_once '../models/Notification.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_error.log');  // klasör var mı kontrol et
error_reporting(E_ALL);


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
    
    // Status güncelle
    Request::updateStatus($id, $status, $resolvePhoto);
    $id = $_POST['id']; // Buradan geldiğine emin misin? Boş olabilir.

    error_log("Gelen POST id: " . var_export($_POST['id'], true));

    
    // Bildirim üret
    $userId = Request::getUserIdByRequestId($id);
    error_log("DEBUG - \$id: " . var_export($id, true));  // Bu satırı ekle
    error_log("DEBUG - \$userId: " . var_export($userId, true));

    if ($userId !== null) {
        $message = "Your request #$id status changed to '$status'";
        $created = Notification::create($userId, $message, $id);
        error_log("DEBUG - Notification created? " . var_export($created, true));
    } else {
        error_log("Warning: User ID not found for request ID $id");
    }


}

// === PANEL İÇİN VERİ ÇEK ===
function showEmployeeRequests() {
    $requests = Request::getAssigned();
    include '../views/employee/request_list.php';
}

if (isset($_GET['action']) && $_GET['action'] === 'show') {
    showEmployeeRequests();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'resolve') {
    $id = $_POST['id'];
    Request::updateStatus($id, 'Resolved');
    
    $userId = Request::getUserIdByRequestId($id);
    if ($userId) {
        $message = "Your request #$id has been resolved";
        Notification::create($userId, $message);
    }
    
    header("Location: ../views/employee/pending_requests.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_status_inline') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    Request::updateStatus($id, $status);
    
    $userId = Request::getUserIdByRequestId($id);
    if ($userId) {
        $message = "Your request #$id status changed to '$status'";
        Notification::create($userId, $message);
    }
    
    $redirectPage = $_POST['from'] ?? 'pending_requests.php';
    header("Location: ../views/employee/$redirectPage");
    exit;
}
?>
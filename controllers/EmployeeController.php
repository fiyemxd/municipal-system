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

    // Durumu güncelle
    Request::updateStatus($id, $status);

    // Kullanıcıya bildirim gönder
    $userId = Request::getUserIdByRequestId($id);
    error_log("Request ID $id için bulunan user_id: " . var_export($userId, true));

    if ($userId) {
        $message = "Your request #$id status changed to '$status'";
        $notificationCreated = Notification::create($userId, $message, $id);
        error_log("Bildirim oluşturma sonucu: " . var_export($notificationCreated, true));
    } else {
        error_log("HATA: Request ID $id için user_id bulunamadı.");
    }

    // Sayfa yönlendirme (default: pending_requests.php)
    $redirectPage = !empty($_POST['from']) ? $_POST['from'] : 'view_requests.php?tab=pending';
    header("Location: ../views/employee/$redirectPage");
    exit;
}



?>
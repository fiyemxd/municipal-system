<?php
session_start();
require_once __DIR__ . '/../../models/Notification.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Tüm bildirimleri getir (0 limit = sınırsız)
    $notifications = Notification::getRecent($_SESSION['user_id'], 0, false);
    
    if (empty($notifications)) {
        echo json_encode([
            'success' => true,
            'notifications' => [],
            'message' => 'No notifications found'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Could not load notifications: ' . $e->getMessage()
    ]);
}
?>
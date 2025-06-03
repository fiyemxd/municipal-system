<?php
require_once __DIR__ . '/../config/db.php';

class Notification {
    public static function create($userId, $message) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        return $stmt->execute([$userId, $message]);
    }

    public static function getUnread($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAllAsRead($userId) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}

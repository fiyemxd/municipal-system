<?php
require_once __DIR__ . '/../config/db.php';

class Notification {
    
    // Bildirim oluştur (request_id opsiyonel)
    public static function create($userId, $message, $requestId = null) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, request_id) VALUES (?, ?, ?)");
            return $stmt->execute([$userId, $message, $requestId]);
        } catch (PDOException $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }

    // Kullanıcının okunmamış bildirimlerini getir
    public static function getUnread($userId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM notifications 
                WHERE user_id = ? AND is_read = 0 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get unread notifications error: " . $e->getMessage());
            return [];
        }
    }
    
    // Kullanıcının tüm bildirimlerini getir, limit varsa uygula
    public static function getRecent($userId, $limit = 10) {
        global $pdo;

        try {
            if ($limit > 0) {
                $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $userId, PDO::PARAM_INT);
                $stmt->bindValue(2, $limit, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$userId]);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent notifications error: " . $e->getMessage());
            return [];
        }
    }

    // Kullanıcının tüm bildirimlerini getir
    public static function getAll($userId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all notifications error: " . $e->getMessage());
            return [];
        }
    }

    // Belirli talebe (request_id) ait bildirimleri getir
    public static function getByRequest($requestId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM notifications 
                WHERE request_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$requestId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get notifications by request error: " . $e->getMessage());
            return [];
        }
    }
    
    // Belirli bildirimi okunmuş olarak işaretle (id sütunu kullanılıyor)
    public static function markAsRead($notificationId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                UPDATE notifications 
                SET is_read = 1 
                WHERE id = ?
            ");
            return $stmt->execute([$notificationId]);
        } catch (PDOException $e) {
            error_log("Mark notification as read error: " . $e->getMessage());
            return false;
        }
    }
    
    // Kullanıcının tüm bildirimlerini okunmuş olarak işaretle
    public static function markAllAsRead($userId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                UPDATE notifications 
                SET is_read = 1 
                WHERE user_id = ? AND is_read = 0
            ");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Mark notifications as read error: " . $e->getMessage());
            return false;
        }
    }

    // Bildirimden talep detayını getirme fonksiyonu (isteğe bağlı, yeni ekleyelim)
    public static function getRequestByNotification($notificationId, $userId) {
        global $pdo;
        try {
            $sql = "SELECT sr.* FROM notifications n
                    JOIN service_requests sr ON n.request_id = sr.id
                    WHERE n.id = ? AND n.user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$notificationId, $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get request by notification error: " . $e->getMessage());
            return false;
        }
    }
    
}

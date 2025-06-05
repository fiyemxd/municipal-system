<?php
require_once __DIR__ . '/../config/db.php';


class Request {

    // === 1. Talep Oluştur ===
    public static function submit($userId, $category, $description, $mediaPath, $latitude, $longitude) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, category, description, media_path, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        return $stmt->execute([$userId, $category, $description, $mediaPath, $latitude, $longitude]);
    }

    // === 2. Vatandaşa Ait Talepleri Getir ===
    public static function getByUser($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === 3. Çalışana Atanmış Tüm Talepler (şu an herkese açık) ===
    public static function getAssigned() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM service_requests ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === 4. Durum Güncelle (ve isteğe bağlı çözüm fotoğrafı ekle) ===
    public static function updateStatus($id, $status, $resolvePhoto = '') {
        global $pdo;
        if ($resolvePhoto !== '') {
            $stmt = $pdo->prepare("UPDATE service_requests SET status = ?, media_path = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$status, $resolvePhoto, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE service_requests SET status = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$status, $id]);
        }
    }


    // === 5. Admin Raporu: Kategoriye Göre Sayım ===
    public static function getStatistics() {
        global $pdo;
        $stmt = $pdo->query("SELECT category, COUNT(*) AS count FROM service_requests GROUP BY category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === 6. Tüm Talepleri Getir (ör. harita için) ===
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM service_requests");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === (İsteğe bağlı) Belirli bir talebi getir ===
    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getPending() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM service_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'Pending'
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getResolved() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM service_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'Resolved'
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getInProgress() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM service_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'In Progress'
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAllWithLocation() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM service_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.latitude IS NOT NULL AND r.longitude IS NOT NULL
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllRequests() {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM service_requests r
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserIdByRequestId($requestId) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM service_requests WHERE id = ?");
            $stmt->execute([$requestId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['user_id'] : null;
        } catch (PDOException $e) {
            error_log("getUserIdByRequestId error: " . $e->getMessage());
            return null;
        }
    }


    public static function getByIdAndUser($id, $userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}

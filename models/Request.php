<?php
require_once 'config/db.php';

class Request {
    public static function submit($userId, $category, $description, $mediaPath, $latitude, $longitude) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, category, description, media_path, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        return $stmt->execute([$userId, $category, $description, $mediaPath, $latitude, $longitude]);
    }

    public static function getByUser($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAssigned() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM service_requests");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatus($id, $status) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public static function getStatistics() {
        global $pdo;
        $stmt = $pdo->query("SELECT category, COUNT(*) AS count FROM service_requests GROUP BY category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

<?php
session_start();
require_once '../models/Request.php';

// GÜVENLİK: Yetkisiz kullanıcıyı engelle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: ../views/login.php");
    exit;
}

// TALP SUBMIT İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'submit') {
    try {
        $category = $_POST['category'] === 'Other'
            ? trim($_POST['custom_category'])
            : $_POST['category'];
        
        $description = $_POST['description'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $userId = $_SESSION['user_id'];
        $mediaPath = '';

        // Medya yükleme işlemi
        if (!empty($_FILES['media']['name'])) {
            $filename = time() . '_' . basename($_FILES['media']['name']);
            $targetDir = __DIR__ . '/../public/uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $targetPath = $targetDir . $filename;
            move_uploaded_file($_FILES['media']['tmp_name'], $targetPath);
            
            $mediaPath = '/public/uploads/' . $filename; // public içinde olduğu için "/uploads/..." yazılmalı
        }

        if (empty($latitude) || empty($longitude)) {
            throw new Exception("Konum alınamadı. Lütfen konum izni verdiğinizden emin olun.");
        } 

        // Veritabanına kaydet
        $result = Request::submit($userId, $category, $description, $mediaPath, $latitude, $longitude);
        
        if (!$result) {
            throw new Exception("Veritabanı ekleme başarısız.");
        }

        header("Location: ../views/citizen/dashboard.php");
        exit;

    } catch (Exception $e) {
        echo "<p>HATA: " . $e->getMessage() . "</p>";
        exit;
    }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'clear_notifications') {
    Notification::markAllAsRead($_SESSION['user_id']);
    header("Location: ../views/citizen/dashboard.php");
    exit;
}

}

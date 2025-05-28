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
        $category = $_POST['category'];
        $description = $_POST['description'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $userId = $_SESSION['user_id'];
        $mediaPath = '';

        // Medya yükleme işlemi
        if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {
            $uploadDir = '../views/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = basename($_FILES['media']['name']);
            $target = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['media']['tmp_name'], $target)) {
                $mediaPath = $target;
            } else {
                throw new Exception("Dosya yüklenemedi.");
            }
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
}

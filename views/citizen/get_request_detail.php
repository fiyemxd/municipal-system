<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$requestId = $_GET['request_id'] ?? null;

if (!$requestId) {
    echo "Request ID missing.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        echo "Request ID $requestId not found.";
        exit;
    }

    // Detayları burda göster
    echo "<h2>Request Details for ID: $requestId</h2>";
    echo "<p>Description: " . htmlspecialchars($request['description'] ?? 'No description') . "</p>";
    echo "<p>Status: " . htmlspecialchars($request['status'] ?? 'Unknown') . "</p>";
    // İstersen diğer alanları da yazdır

} catch (PDOException $e) {
    error_log("Error fetching request detail: " . $e->getMessage());
    echo "Error occurred.";
}   

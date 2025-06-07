<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$requestId = $_GET['id'] ?? null;

if (!$requestId) {
    echo json_encode([
        'success' => false,
        'message' => 'Request ID missing.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        echo json_encode([
            'success' => false,
            'message' => "Request ID $requestId not found."
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'request' => $request
    ]);
} catch (PDOException $e) {
    error_log("Error fetching request detail: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error occurred while fetching request.'
    ]);
}


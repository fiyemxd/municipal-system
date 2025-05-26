<?php
$host = 'localhost';
$db = 'municipality';
$user = 'root';
$pass = 'root'; // MAMP default şifresi
$port = '8889'; // MAMP default MySQL portu

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
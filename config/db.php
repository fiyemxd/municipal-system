<?php
// Veritabanı bağlantısı için PDO nesnesi oluşturulur ve global $pdo değişkenine atanır

$host = 'localhost';
$db   = 'municipality';
$user = 'root';
$pass = 'root';
$port = '8889';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8", 
        $user, 
        $pass
    );
    // Hata ayıklama modu: Exception olarak ayarlanır
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası durumunda script durur ve hata mesajı gösterilir
    die("Database connection failed: " . $e->getMessage());
}
?>

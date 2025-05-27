<?php
session_start();
require_once '../config/db.php';

// === REGISTER İŞLEMİ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = md5($_POST['new_password']); // Güvenlik için gerçek projede password_hash() tercih edilir
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$newUsername, $newPassword, $role]);
        echo "<p>Registration successful. <a href='../views/login.php'>Login here</a></p>";
        exit;
    } catch (PDOException $e) {
        echo "<p>Registration error: " . $e->getMessage() . "</p>";
        exit;
    }
}

// === LOGIN İŞLEMİ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'citizen':
                    header("Location: ../views/citizen/dashboard.php");
                    exit;
                case 'employee':
                    header("Location: ../views/employee/request_list.php");
                    exit;
                case 'admin':
                    header("Location: ../views/admin/reports.php");
                    exit;
            }
        } else {
            echo "<p>Incorrect username or password. <a href='../views/login.php'>Try again</a></p>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Login error: " . $e->getMessage();
        exit;
    }
}

// === LOGOUT İŞLEMİ ===
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}

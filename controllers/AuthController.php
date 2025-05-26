<?php
session_start();
require_once '../config/db.php';

// REGISTER işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = md5($_POST['new_password']); // Not: Gerçek projede password_hash() önerilir
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$newUsername, $newPassword, $role]);

        echo "<p style='color:green;'>Registration successful. You can now <a href='../views/login.php'>log in</a>.</p>";
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<p style='color:red;'>Username already exists.</p>";
        } else {
            echo "<p style='color:red;'>Registration error: " . $e->getMessage() . "</p>";
        }
        echo "<a href='../views/register.php'>Back to Register</a>";
        exit;
    }
}

// LOGIN İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Not: Gerçek projede password_hash() kullanman önerilir

    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Oturum başlat
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Rol bazlı yönlendirme
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
                default:
                    echo "Unknown role!";
            }
        } else {
            echo "<p style='color:red;'>Incorrect username or password</p>";
            echo "<a href='../views/login.php'>Back to login</a>";
        }
    } catch (PDOException $e) {
        echo "Login error: " . $e->getMessage();
    }
}

// LOGOUT İŞLEMİ
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}
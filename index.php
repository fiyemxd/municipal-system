<?php
// Oturum varsa role göre yönlendir
session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'citizen': header("Location: views/citizen/dashboard.php"); break;
        case 'employee': header("Location: views/employee/request_list.php"); break;
        case 'admin': header("Location: views/admin/reports.php"); break;
    }
} else {
    header("Location: views/login.php");
}
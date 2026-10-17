<?php
// File: admin_quanli/check_loginadmin.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
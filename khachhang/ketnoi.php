<?php
if (defined('KETNOI_LOADED')) return;
define('KETNOI_LOADED', true);

if (session_status() === PHP_SESSION_NONE) session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quanlikhachsan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

if (!function_exists('ghiLichSu')) {
    function ghiLichSu($conn, $admin_id, $hanhdong, $bang, $doituong_id, $noidung) {
        $stmt = $conn->prepare("INSERT INTO lichsu (admin_id, hanhdong, bang, doituong_id, noidung) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issis", $admin_id, $hanhdong, $bang, $doituong_id, $noidung);
        $stmt->execute();
    }
}
?>
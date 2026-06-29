<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';

if ($id && $status) {
    $conn->query("UPDATE orders SET status = '$status' WHERE id = '$id'");
}

header("Location: admin_orders.php");
exit();
?>
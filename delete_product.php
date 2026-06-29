<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'seller') {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id = '$id' AND seller_id = '{$_SESSION['user_id']}'");
header("Location: profile.php");
exit();
?>
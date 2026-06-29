<?php
include 'db_connect.php';
$product_id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT stock FROM products WHERE id = '$product_id'");
$stock = $result->fetch_assoc()['stock'] ?? 0;
echo json_encode(['stock' => $stock]);
?>
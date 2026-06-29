<?php
header('Content-Type: application/json');
include 'db_connect.php';

$product_id = $_GET['id'] ?? 0;

// Get average rating and total reviews
$avg = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id = '$product_id'")->fetch_assoc();

echo json_encode([
    'rating' => round($avg['avg'] ?? 0, 1),
    'reviews' => $avg['total'] ?? 0
]);
?>
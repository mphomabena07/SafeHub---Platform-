<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$review_id = $_GET['id'] ?? 0;
$product_id = $_GET['product_id'] ?? 1;
$user_id = $_SESSION['user_id'];

// Delete review only if it belongs to this user
$conn->query("DELETE FROM reviews WHERE id = '$review_id' AND user_id = '$user_id'");

// Redirect back to product page
header("Location: product_details.php?id=$product_id&review=deleted");
exit();
?>
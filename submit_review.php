<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    
    // ============================================
    // VALIDATION: Check for empty fields
    // ============================================
    if (empty($rating)) {
        header("Location: product_details.php?id=$product_id&error=no_rating");
        exit();
    }
    
    if (empty($comment)) {
        header("Location: product_details.php?id=$product_id&error=no_comment");
        exit();
    }
    
    // Get the seller_id from the product
    $product_query = $conn->query("SELECT seller_id FROM products WHERE id = '$product_id'");
    $product_data = $product_query->fetch_assoc();
    $seller_id = $product_data['seller_id'] ?? 1;
    
    // Insert the review with seller_id
    $sql = "INSERT INTO reviews (product_id, seller_id, user_id, user_name, rating, comment) 
            VALUES ('$product_id', '$seller_id', '$user_id', '$user_name', '$rating', '$comment')";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect back to product page with success message
        header("Location: product_details.php?id=$product_id&review=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    // If someone tries to access this file directly without POST
    header("Location: index.php");
    exit();
}
?>
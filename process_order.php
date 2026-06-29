<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Get form data
$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$pickup_point = $_POST['pickup_point'] ?? '';
$cart_data = $_POST['cart_data'] ?? '[]';

// Decode cart data
$cart = json_decode($cart_data, true);

if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

// ============================================
// CHECK: Prevent sellers from buying their own products
// ============================================
foreach ($cart as $item) {
    $product_id = $item['id'];
    $product_check = $conn->query("SELECT seller_id FROM products WHERE id = '$product_id'");
    
    if ($product_check && $product_check->num_rows > 0) {
        $product_data = $product_check->fetch_assoc();
        $seller_id = $product_data['seller_id'];
        
        // If the buyer is the seller, show error and stop
        if ($seller_id == $user_id) {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Cannot Buy Your Own Product - SafeHub</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
                    .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
                    h1 { color: #ff4444; margin-bottom: 20px; }
                    .btn { display: inline-block; padding: 12px 24px; background: #2E7D32; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; margin: 5px; }
                    .btn:hover { background: #1B5E20; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>⛔ Cannot Buy Your Own Product</h1>
                    <p>You cannot purchase a product that you are selling.</p>
                    <p style="margin: 20px 0; color: #666;">Please remove this item from your cart and try again.</p>
                    <a href="cart.php" class="btn">← Back to Cart</a>
                </div>
            </body>
            </html>
            <?php
            exit();
        }
    }
}

// Calculate total
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order into database
$sql = "INSERT INTO orders (user_id, total, status, created_at) VALUES ('$user_id', '$total', 'pending', NOW())";

if ($conn->query($sql) === TRUE) {
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($cart as $item) {
        $product_name = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $conn->query("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES ('$order_id', '$product_name', '$quantity', '$price')");
    }
    
    // Redirect to confirmation page
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
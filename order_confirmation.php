<?php
session_start();
include 'db_connect.php';

$order_id = $_GET['order_id'] ?? 0;

// Get order details
$order = $conn->query("SELECT * FROM orders WHERE id = '$order_id'")->fetch_assoc();

if (!$order) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - SafeHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; padding-bottom: 80px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .success-icon { font-size: 4rem; color: #2E7D32; margin-bottom: 20px; }
        h1 { color: #2E7D32; margin-bottom: 20px; }
        .order-details { background: #f9f9f9; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left; }
        .order-details p { margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 30px; background: #2E7D32; color: white; border: none; border-radius: 30px; cursor: pointer; font-size: 1rem; text-decoration: none; }
        .btn:hover { background: #1B5E20; }
        .escrow-note { background: #DCFCE7; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .bottom-nav a { text-decoration: none; color: #666; font-size: 0.85rem; display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .bottom-nav a:hover { color: #2E7D32; }
    </style>
</head>
<body>

<div class="container">
    <div class="success-icon">✅</div>
    <h1>Order Confirmed!</h1>
    <p>Thank you for your order. Your payment will be held in escrow until you confirm delivery.</p>

    <div class="order-details">
        <p><strong>Order #:</strong> SA-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Total:</strong> R<?php echo number_format($order['total'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
        <p><strong>Date:</strong> <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
    </div>

    <div class="escrow-note">
        🔒 Your payment is held securely in escrow.<br>
        It will be released to the seller once you confirm delivery.
    </div>

    <a href="https://safehub4.great-site.net/" class="btn">Continue Shopping</a>
</div>

<!-- ===== BOTTOM NAVIGATION ===== -->
<div class="bottom-nav">
    <a href="https://safehub4.great-site.net/">🏠 Home</a>
    <a href="cart.php">🛒 Cart</a>
    <a href="profile.php">👤 Profile</a>
    <a href="login.php">🔐 Login</a>
</div>

<script>
    // Clear the cart on order confirmation
    localStorage.removeItem('safehubCart');
</script>

</body>
</html>
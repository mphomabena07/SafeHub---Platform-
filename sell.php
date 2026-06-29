<?php
session_start();

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'sell.php';
    header("Location: login.php");
    exit();
}

// Check if user is a seller
if ($_SESSION['user_role'] == 'buyer') {
    // Ask user to upgrade to seller
    if (isset($_GET['upgrade']) && $_GET['upgrade'] == 'yes') {
        include 'db_connect.php';
        $user_id = $_SESSION['user_id'];
        $conn->query("UPDATE users SET role = 'seller' WHERE id = '$user_id'");
        $_SESSION['user_role'] = 'seller';
        header("Location: sell.php");
        exit();
    }
    
    // Show upgrade message
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Upgrade to Seller - SafeHub</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: "Segoe UI", sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
            .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
            h1 { color: #2E7D32; margin-bottom: 20px; }
            .btn { display: inline-block; padding: 12px 24px; background: #2E7D32; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; margin: 5px; }
            .btn:hover { background: #1B5E20; }
            .btn-cancel { background: #666; }
            .btn-cancel:hover { background: #444; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔄 Upgrade to Seller</h1>
            <p>To list products on SafeHub, you need to upgrade your account from <strong>Buyer</strong> to <strong>Seller</strong>.</p>
            <p style="margin: 20px 0; color: #666;">This will allow you to create product listings, manage inventory, and receive payments.</p>
            <a href="sell.php?upgrade=yes" class="btn">✅ Upgrade to Seller</a>
            <a href="index.php" class="btn btn-cancel">← Cancel</a>
        </div>
    </body>
    </html>';
    exit();
}

// If user is already a seller, proceed with selling
include 'db_connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $seller_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO products (seller_id, name, price, description, stock) 
            VALUES ('$seller_id', '$name', '$price', '$description', '$stock')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Product listed successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Sell Now</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; padding-bottom: 80px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2E7D32; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #2E7D32; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background: #1B5E20; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #2E7D32; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
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
        <h1>🛒 Sell Your Product</h1>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="e.g. Vetkoek" required>
            </div>
            <div class="form-group">
                <label>Price (R)</label>
                <input type="number" name="price" placeholder="e.g. 25" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Describe your product"></textarea>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" placeholder="e.g. 10" value="10" required>
            </div>
            <button type="submit" class="btn">List Product</button>
        </form>
        <a href="profile.php" class="back-link">← Back to Profile</a>
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>

    <div class="bottom-nav">
        <a href="index.php">🏠 Home</a>
        <a href="cart.php">🛒 Cart</a>
        <a href="profile.php">👤 Profile</a>
        <a href="login.php">🔐 Login</a>
        <a href="register.php">📝 Register</a>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'seller') {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$id = $_GET['id'];
$message = '';

// Get product
$product = $conn->query("SELECT * FROM products WHERE id = '$id' AND seller_id = '{$_SESSION['user_id']}'")->fetch_assoc();
if (!$product) {
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    
    $sql = "UPDATE products SET name='$name', price='$price', description='$description', stock='$stock' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Product updated!";
        $product = $conn->query("SELECT * FROM products WHERE id = '$id'")->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Edit Product</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2E7D32; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #2E7D32; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background: #1B5E20; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 15px; background: #d4edda; color: #155724; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #2E7D32; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Product</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Price (R)</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?php echo $product['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
            </div>
            <button type="submit" class="btn">Update Product</button>
        </form>
        <a href="profile.php" class="back-link">← Back to Profile</a>
    </div>
</body>
</html>
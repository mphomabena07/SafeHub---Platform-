<?php
session_start();

// If user is not logged in, save the redirect URL and send to login/register
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeHub - Checkout</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 20px; padding-bottom: 80px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2E7D32; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #2E7D32; }
        .back-link { color: #2E7D32; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .escrow-note { background: #DCFCE7; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .btn { width: 100%; padding: 12px; background: #2E7D32; color: white; border: none; border-radius: 30px; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: #1B5E20; }
        .cart-total { text-align: right; font-size: 1.5rem; font-weight: bold; color: #2E7D32; margin: 20px 0; }
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
    <div class="header">
        <h1>📦 Checkout</h1>
        <a href="cart.php" class="back-link">← Back to Cart</a>
    </div>

    <!-- Checkout Form -->
    <form method="POST" action="process_order.php" id="checkoutForm">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" id="fullName" placeholder="Thabo Mokoena" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" id="phone" placeholder="071 234 5678" required>
        </div>
        <div class="form-group">
            <label>Pickup Point</label>
            <select name="pickup_point">
                <option>Khumalo Spaza - Soweto</option>
                <option>Ncube Spaza - Tembisa</option>
                <option>Dlamini Market - Durban</option>
            </select>
        </div>

        <div class="escrow-note">
            🔒 Your payment will be held in escrow until you confirm delivery
        </div>

        <div class="cart-total" id="checkoutTotal">Total: R0</div>

        <!-- Hidden field to store cart data -->
        <input type="hidden" name="cart_data" id="cartData">

        <button type="submit" class="btn" id="confirmBtn">Confirm Order & Pay</button>
    </form>
</div>

<!-- ===== BOTTOM NAVIGATION ===== -->
<div class="bottom-nav">
    <a href="https://safehub4.great-site.net/">🏠 Home</a>
    <a href="cart.php">🛒 Cart</a>
    <a href="profile.php">👤 Profile</a>
    <a href="login.php">🔐 Login</a>
</div>

<script>
    // Calculate total from localStorage
    function getCartData() {
        const cart = JSON.parse(localStorage.getItem('safehubCart') || '[]');
        return cart;
    }

    function getTotal() {
        const cart = getCartData();
        return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    // Set the total on page load
    document.getElementById('checkoutTotal').textContent = `Total: R${getTotal()}`;

    // Before submitting the form, store cart data in the hidden field
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const cart = getCartData();
        if (cart.length === 0) {
            e.preventDefault();
            alert('Your cart is empty.');
            return;
        }
        
        const name = document.getElementById('fullName').value;
        if (!name) {
            e.preventDefault();
            alert('Please enter your name.');
            return;
        }
        
        // Store cart data in hidden field (as JSON string)
        document.getElementById('cartData').value = JSON.stringify(cart);
    });
</script>

</body>
</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeHub - Shopping Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #0F7A3E;
            --primary-dark: #0A5C2E;
            --primary-light: #E8F5E9;
            --secondary: #FF6B35;
            --secondary-hover: #E55A2B;
            --dark: #1A1A2E;
            --grey: #6B7280;
            --light-grey: #F3F4F6;
            --white: #FFFFFF;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.15);
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: var(--dark);
            padding-bottom: 70px;
        }

        .header {
            background: var(--white);
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo { 
            font-size: 1.3rem; 
            font-weight: 800; 
            color: var(--primary); 
            text-decoration: none; 
        }
        .logo span { color: var(--secondary); }
        .tagline { 
            font-size: 0.7rem; 
            color: var(--grey); 
            margin-left: 8px; 
            font-weight: 400; 
        }

        .back-link { 
            color: var(--grey); 
            text-decoration: none; 
            font-weight: 500; 
            transition: var(--transition); 
        }
        .back-link:hover { color: var(--primary); }

        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 24px; 
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .cart-header h1 { font-size: 1.8rem; }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
            gap: 16px;
            flex-wrap: wrap;
        }
        .cart-item .name { font-weight: 600; flex: 2; }
        .cart-item .price { color: var(--primary); font-weight: 700; flex: 1; }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: var(--light-grey);
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        .qty-btn:hover { background: #ddd; }
        .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .remove-btn {
            background: #DC2626;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }
        .remove-btn:hover { background: #B91C1C; }

        .cart-total {
            text-align: right;
            padding: 20px 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            border-top: 2px solid #eee;
            margin-top: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 0;
            color: var(--grey);
        }
        .empty-cart .icon { font-size: 4rem; display: block; margin-bottom: 16px; }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-primary {
            background: var(--secondary);
            color: white;
            width: 100%;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: var(--secondary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,107,53,0.3);
        }
        .btn-primary:disabled { 
            opacity: 0.5; 
            cursor: not-allowed; 
            transform: none !important; 
        }

        .btn-outline {
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover { 
            background: var(--primary); 
            color: white; 
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--white);
            border-top: 1px solid rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-around;
            padding: 8px 12px;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .bottom-nav a {
            text-decoration: none;
            color: var(--grey);
            font-size: 0.7rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            transition: var(--transition);
            padding: 4px 8px;
            border-radius: var(--radius-sm);
            min-width: 50px;
        }
        .bottom-nav a i { font-size: 1.2rem; transition: var(--transition); }
        .bottom-nav a:hover { color: var(--primary); }
        .bottom-nav a:hover i { transform: translateY(-2px); }
        .bottom-nav a.active { color: var(--primary); font-weight: 600; }
        .bottom-nav a.active i { color: var(--primary); }

        .stock-warning { 
            color: #F59E0B; 
            font-size: 0.8rem; 
            margin-top: 4px; 
        }

        @media (max-width: 600px) {
            .cart-item { flex-direction: column; align-items: stretch; gap: 8px; }
            .cart-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .tagline { display: none; }
        }
        @media (max-width: 480px) {
            .bottom-nav a { font-size: 0.6rem; min-width: 40px; }
            .bottom-nav a i { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <header class="header">
        <div>
            <a href="/" class="logo">Safe<span>Hub</span></a>
            <span class="tagline">Shop local, sell fast</span>
        </div>
        <a href="/" class="back-link"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </header>

    <div class="container">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Your Cart</h1>
            <span style="color: var(--grey); font-size: 0.9rem;" id="itemCount">0 items</span>
        </div>

        <div id="cartItems"></div>
        <div class="cart-total" id="cartTotal">Total: R0</div>

        <button class="btn btn-primary" id="checkoutBtn"><i class="fas fa-credit-card"></i> Proceed to Checkout</button>
    </div>

    <div class="bottom-nav">
        <a href="/"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i><span>Cart</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
        <a href="register.php"><i class="fas fa-user-plus"></i><span>Register</span></a>
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i><span>Admin</span></a>
    </div>

    <script>
        const storageKey = 'safehubCart';

        function loadCart() {
            return JSON.parse(localStorage.getItem(storageKey) || '[]');
        }

        function saveCart(cart) {
            localStorage.setItem(storageKey, JSON.stringify(cart));
            renderCart();
        }

        function getStock(productId) {
            return fetch(`get_stock.php?id=${productId}`)
                .then(response => response.json())
                .then(data => data.stock)
                .catch(() => 10);
        }

        function renderCart() {
            const cart = loadCart();
            const container = document.getElementById('cartItems');
            const totalContainer = document.getElementById('cartTotal');
            const itemCount = document.getElementById('itemCount');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                        Your cart is empty
                        <br><br>
                        <a href="/" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Start Shopping</a>
                    </div>
                `;
                totalContainer.textContent = 'Total: R0';
                itemCount.textContent = '0 items';
                document.getElementById('checkoutBtn').disabled = true;
                return;
            }

            let html = '';
            let total = 0;
            const itemCountTotal = cart.reduce((sum, item) => sum + item.quantity, 0);
            itemCount.textContent = `${itemCountTotal} items`;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                html += `
                    <div class="cart-item" data-index="${index}">
                        <span class="name">${item.name}</span>
                        <span class="price">R${item.price}</span>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQty(${index}, -1)" id="qty-minus-${index}"><i class="fas fa-minus"></i></button>
                            <span id="qty-value-${index}">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQty(${index}, 1)" id="qty-plus-${index}"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="remove-btn" onclick="removeItem(${index})"><i class="fas fa-trash"></i> Remove</button>
                        <div class="stock-warning" id="stock-warning-${index}"></div>
                    </div>
                `;
            });

            container.innerHTML = html;
            totalContainer.textContent = `Total: R${total}`;
            document.getElementById('checkoutBtn').disabled = false;

            cart.forEach((item, index) => {
                getStock(item.id).then(stock => {
                    const qtySpan = document.getElementById(`qty-value-${index}`);
                    const minusBtn = document.getElementById(`qty-minus-${index}`);
                    const plusBtn = document.getElementById(`qty-plus-${index}`);
                    const warning = document.getElementById(`stock-warning-${index}`);
                    
                    if (item.quantity >= stock) {
                        plusBtn.disabled = true;
                        if (warning) warning.textContent = `⚠️ Only ${stock} available`;
                    }
                    if (item.quantity <= 1) minusBtn.disabled = true;
                });
            });
        }

        function updateQty(index, change) {
            const cart = loadCart();
            if (!cart[index]) return;
            const newQty = cart[index].quantity + change;
            if (newQty < 1) { removeItem(index); return; }
            getStock(cart[index].id).then(stock => {
                if (newQty > stock) { alert(`Only ${stock} available.`); return; }
                cart[index].quantity = newQty;
                saveCart(cart);
            });
        }

        function removeItem(index) {
            let cart = loadCart();
            cart.splice(index, 1);
            saveCart(cart);
        }

        document.getElementById('checkoutBtn').addEventListener('click', async () => {
            const cart = loadCart();
            if (cart.length === 0) { alert('Your cart is empty.'); return; }
            let outOfStock = false;
            for (let item of cart) {
                const stock = await getStock(item.id);
                if (item.quantity > stock) {
                    outOfStock = true;
                    alert(`Only ${stock} of "${item.name}" available. Please update your cart.`);
                    break;
                }
            }
            if (!outOfStock) { window.location.href = 'checkout.php'; }
        });

        renderCart();
    </script>
</body>
</html>
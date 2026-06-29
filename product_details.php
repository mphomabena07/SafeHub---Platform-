<?php
session_start();
include 'db_connect.php';
$product_id = $_GET['id'] ?? 1;

// Get product details
$product = $conn->query("SELECT * FROM products WHERE id = '$product_id'")->fetch_assoc();

// Get seller ID from product
$seller_id = $product['seller_id'] ?? 1;

// Get reviews for this product
$reviews = $conn->query("SELECT * FROM reviews WHERE product_id = '$product_id' ORDER BY created_at DESC");

// Calculate average rating and total reviews for this product
$avg = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id = '$product_id'")->fetch_assoc();
$avg_rating = round($avg['avg'] ?? 0, 1);
$total_reviews = $avg['total'] ?? 0;

$seller_data = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                             FROM reviews WHERE seller_id = '$seller_id'")->fetch_assoc();
$seller_rating = round($seller_data['avg_rating'] ?? 0, 1);
$seller_total_reviews = $seller_data['total_reviews'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeHub - <?php echo $product['name']; ?></title>
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

        .logo { font-size: 1.3rem; font-weight: 800; color: var(--primary); text-decoration: none; }
        .logo span { color: var(--secondary); }
        .tagline { font-size: 0.7rem; color: var(--grey); margin-left: 8px; font-weight: 400; }

        .back-link {
            color: var(--grey);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-link:hover { color: var(--primary); }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: var(--shadow);
        }

        .product-image-wrapper {
            position: relative;
            padding-top: 100%;
            background: var(--light-grey);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-name { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
        .product-rating { display: flex; align-items: center; gap: 8px; color: var(--grey); font-size: 0.9rem; margin-bottom: 16px; }
        .stars { color: #F59E0B; }
        .product-price { font-size: 2.2rem; font-weight: 700; color: var(--primary); margin-bottom: 16px; }

        .stock-info {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .in-stock { background: var(--primary-light); color: var(--primary); }
        .out-of-stock { background: #FEE2E2; color: #DC2626; }

        .seller-info {
            background: var(--light-grey);
            padding: 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
        }
        .seller-info .verified { color: var(--primary); font-weight: 600; }

        .delivery-info {
            background: var(--light-grey);
            padding: 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
        }

        .escrow-note {
            background: var(--primary-light);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            text-align: center;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 20px;
        }
        .escrow-note i { margin-right: 8px; }

        .quantity-section {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .quantity-section label { font-weight: 600; }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #ddd;
            border-radius: var(--radius-sm);
            padding: 4px;
        }
        .qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: var(--light-grey);
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            color: var(--dark);
        }
        .qty-btn:hover { background: #ddd; }
        .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .qty-value { min-width: 30px; text-align: center; font-weight: 600; font-size: 1.1rem; }

        .button-group { display: flex; gap: 16px; flex-wrap: wrap; }
        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 140px;
        }
        .btn-primary { background: var(--secondary); color: white; }
        .btn-primary:hover { background: var(--secondary-hover); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,107,53,0.3); }
        .btn-outline { background: var(--white); color: var(--primary); border: 2px solid var(--primary); }
        .btn-outline:hover { background: var(--primary); color: white; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

        .description {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px 32px;
            margin-top: 24px;
            box-shadow: var(--shadow);
        }
        .description h3 { margin-bottom: 12px; }

        .reviews-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px;
            margin-top: 24px;
            box-shadow: var(--shadow);
        }
        .review { padding: 16px 0; border-bottom: 1px solid #eee; }
        .review:last-child { border-bottom: none; }
        .review .user { font-weight: 600; }
        .review .stars { color: #F59E0B; }
        .review .date { color: var(--grey); font-size: 0.8rem; }

        .review-form {
            background: var(--light-grey);
            padding: 20px;
            border-radius: var(--radius-sm);
            margin-top: 16px;
        }
        .review-form select, .review-form textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            margin-bottom: 12px;
        }
        .review-form button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .review-form button:hover { background: var(--primary-dark); }

        .btn-delete-review {
            display: inline-block;
            padding: 4px 12px;
            background: #DC2626;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.75rem;
            margin-top: 8px;
            transition: var(--transition);
        }
        .btn-delete-review:hover { background: #B91C1C; }

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

        .message {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message-success { background: var(--primary-light); color: var(--primary); }
        .message-error { background: #FEE2E2; color: #DC2626; }
        .message-delete { background: #FEE2E2; color: #DC2626; }

        @media (max-width: 768px) {
            .product-details { grid-template-columns: 1fr; padding: 20px; gap: 24px; }
            .product-name { font-size: 1.5rem; }
            .product-price { font-size: 1.8rem; }
            .button-group { flex-direction: column; }
            .btn { min-width: 100%; }
            .container { padding: 16px; }
            .reviews-section { padding: 20px; }
            .tagline { display: none; }
        }
        @media (max-width: 480px) {
            .bottom-nav a { font-size: 0.6rem; min-width: 40px; }
            .bottom-nav a i { font-size: 1rem; }
            .product-details { padding: 16px; }
            .description { padding: 16px; }
        }
    </style>
</head>
<body>

    <header class="header">
        <div>
            <a href="/" class="logo">Safe<span>Hub</span></a>
            <span class="tagline">Shop local, sell fast</span>
        </div>
        <a href="cart.php" class="back-link"><i class="fas fa-shopping-cart"></i> Cart</a>
    </header>

    <div class="container">
        <a href="/" class="back-link" style="display:inline-flex; margin-bottom:16px; align-items:center; gap:6px;">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <?php if (isset($_GET['review']) && $_GET['review'] == 'success'): ?>
            <div class="message message-success"><i class="fas fa-check-circle"></i> Thank you! Your review has been submitted.</div>
        <?php endif; ?>
        <?php if (isset($_GET['review']) && $_GET['review'] == 'deleted'): ?>
            <div class="message message-delete"><i class="fas fa-trash"></i> Your review has been deleted.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'no_rating'): ?>
            <div class="message message-error"><i class="fas fa-exclamation-circle"></i> Please select a rating.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'no_comment'): ?>
            <div class="message message-error"><i class="fas fa-exclamation-circle"></i> Please write a comment.</div>
        <?php endif; ?>

        <div class="product-details">
            <div class="product-image-wrapper">
                <img class="product-image" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            <div>
                <h1 class="product-name"><?php echo $product['name']; ?></h1>
                <div class="product-rating">
                    <span class="stars"><i class="fas fa-star"></i> <?php echo $avg_rating; ?></span>
                    <span>(<?php echo $total_reviews; ?> reviews)</span>
                </div>
                <div class="product-price">R<?php echo number_format($product['price'], 2); ?></div>

                <div class="stock-info <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                    <?php if ($product['stock'] > 0): ?>
                        <i class="fas fa-check-circle"></i> In stock (<?php echo $product['stock']; ?> available)
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i> Out of stock
                    <?php endif; ?>
                </div>

                <div class="seller-info">
                    <strong><i class="fas fa-store"></i> Seller:</strong> <?php echo $product['seller_name'] ?? 'SafeHub Seller'; ?><br>
                    <span class="verified"><i class="fas fa-check-circle"></i> Verified seller</span>
                </div>

                <div class="delivery-info">
                    <strong><i class="fas fa-truck"></i> Delivery</strong><br>
                    <?php echo $product['delivery_info'] ?? 'Pick up from Ncube Spaza - Soweto. Free delivery to pickup point.'; ?>
                </div>

                <div class="escrow-note">
                    <i class="fas fa-lock"></i> Payment held in escrow until you confirm delivery
                </div>

                <div class="quantity-section">
                    <label><i class="fas fa-hashtag"></i> Quantity</label>
                    <div class="quantity-selector">
                        <button class="qty-btn" id="decrementQty" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>><i class="fas fa-minus"></i></button>
                        <span class="qty-value" id="qtyValue">1</span>
                        <button class="qty-btn" id="incrementQty" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-outline" id="addToCartBtn" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>><i class="fas fa-cart-plus"></i> Add to Cart</button>
                    <button class="btn btn-primary" id="buyNowBtn" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>><i class="fas fa-bolt"></i> Buy Now</button>
                </div>
            </div>
        </div>

        <div class="description">
            <h3><i class="fas fa-file-alt"></i> Description</h3>
            <p><?php echo $product['description']; ?></p>
        </div>

        <div class="reviews-section" id="review-section">
            <h3><i class="fas fa-star"></i> Customer Reviews (<?php echo $total_reviews; ?>)</h3>

            <?php if ($reviews && $reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review">
                        <div class="user"><i class="fas fa-user"></i> <?php echo $review['user_name']; ?></div>
                        <div class="stars"><?php echo str_repeat('<i class="fas fa-star"></i>', $review['rating']) . str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']); ?></div>
                        <div><?php echo $review['comment']; ?></div>
                        <div class="date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <a href="delete_review.php?id=<?php echo $review['id']; ?>&product_id=<?php echo $product_id; ?>" 
                               class="btn-delete-review" 
                               onclick="return confirm('Are you sure you want to delete your review?')"><i class="fas fa-trash"></i> Delete Review</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: var(--grey);">No reviews yet. Be the first to review this product!</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="review-form">
                    <h4><i class="fas fa-edit"></i> Write a Review</h4>
                    <form action="submit_review.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <select name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="5"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> (5)</option>
                            <option value="4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> (4)</option>
                            <option value="3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> (3)</option>
                            <option value="2"><i class="fas fa-star"></i><i class="fas fa-star"></i> (2)</option>
                            <option value="1"><i class="fas fa-star"></i> (1)</option>
                        </select>
                        <textarea name="comment" rows="3" placeholder="Write your review here..." required></textarea>
                        <button type="submit"><i class="fas fa-paper-plane"></i> Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <p style="margin-top:16px;">
                    <a href="login.php?redirect=<?php echo urlencode('product_details.php?id=' . $product_id . '#review-section'); ?>" style="color:var(--primary); font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fas fa-sign-in-alt"></i> Log in
                    </a> to write a review.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="/"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>Cart</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
        <a href="register.php"><i class="fas fa-user-plus"></i><span>Register</span></a>
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i><span>Admin</span></a>
    </div>

    <script>
        let quantity = 1;
        const maxStock = <?php echo $product['stock'] ?? 0; ?>;
        const productId = <?php echo $product_id; ?>;
        const productName = "<?php echo $product['name']; ?>";
        const productPrice = <?php echo $product['price']; ?>;

        document.getElementById('decrementQty').addEventListener('click', () => {
            if (quantity > 1) { quantity--; document.getElementById('qtyValue').textContent = quantity; }
        });

        document.getElementById('incrementQty').addEventListener('click', () => {
            if (quantity < maxStock) { quantity++; document.getElementById('qtyValue').textContent = quantity; }
            else { alert(`Only ${maxStock} of this item available.`); }
        });

        function getStock(productId) {
            return fetch(`get_stock.php?id=${productId}`)
                .then(response => response.json())
                .then(data => data.stock)
                .catch(() => maxStock);
        }

        document.getElementById('addToCartBtn').addEventListener('click', async function() {
            if (maxStock <= 0) { alert('Sorry, this item is out of stock.'); return; }
            const currentStock = await getStock(productId);
            let cart = JSON.parse(localStorage.getItem('safehubCart') || '[]');
            const existingItem = cart.find(item => item.id === productId);
            const currentQuantity = existingItem ? existingItem.quantity : 0;
            if (currentQuantity + quantity > currentStock) {
                alert(`Sorry, only ${currentStock} available. You have ${currentQuantity} in your cart.`);
                return;
            }
            if (existingItem) { existingItem.quantity += quantity; }
            else { cart.push({ id: productId, name: productName, price: productPrice, quantity: quantity }); }
            localStorage.setItem('safehubCart', JSON.stringify(cart));
            alert(`${quantity} x ${productName} added to cart!`);
        });

        document.getElementById('buyNowBtn').addEventListener('click', async function() {
            if (maxStock <= 0) { alert('Sorry, this item is out of stock.'); return; }
            const currentStock = await getStock(productId);
            let cart = JSON.parse(localStorage.getItem('safehubCart') || '[]');
            const existingItem = cart.find(item => item.id === productId);
            const currentQuantity = existingItem ? existingItem.quantity : 0;
            if (currentQuantity + quantity > currentStock) {
                alert(`Sorry, only ${currentStock} available. You have ${currentQuantity} in your cart.`);
                return;
            }
            if (existingItem) { existingItem.quantity += quantity; }
            else { cart.push({ id: productId, name: productName, price: productPrice, quantity: quantity }); }
            localStorage.setItem('safehubCart', JSON.stringify(cart));
            window.location.href = 'cart.php';
        });
    </script>
</body>
</html>
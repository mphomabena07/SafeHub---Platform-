<?php
session_start();
include 'db_connect.php';

// Get all products from database
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeHub - Shop Local, Sell Fast</title>
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

        /* ===== HEADER ===== */
        header {
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

        .logo { display: flex; align-items: center; gap: 8px; }
        .logo h1 { font-size: 1.3rem; font-weight: 800; color: var(--primary); cursor: pointer; }
        .logo h1 span { color: var(--secondary); }
        .tagline { font-size: 0.7rem; color: var(--grey); font-weight: 400; margin-left: 4px; }

        .header-actions { display: flex; gap: 16px; align-items: center; }
        .header-actions a { color: var(--dark); text-decoration: none; font-size: 1.2rem; transition: var(--transition); position: relative; }
        .header-actions a:hover { color: var(--primary); }
        .cart-badge { background: var(--secondary); color: white; border-radius: 50%; padding: 1px 6px; font-size: 0.6rem; font-weight: 700; position: absolute; top: -6px; right: -10px; }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before { content: ''; position: absolute; top: -50%; right: -20%; width: 60%; height: 200%; background: rgba(255,255,255,0.05); border-radius: 50%; transform: rotate(-15deg); }
        .hero h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; position: relative; }
        .hero p { font-size: 1.1rem; opacity: 0.9; position: relative; }
        .hero .subtitle { font-size: 0.95rem; opacity: 0.7; margin-top: -4px; margin-bottom: 24px; position: relative; }

        .hero-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; position: relative; }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }
        .btn-primary { background: var(--secondary); color: white; }
        .btn-primary:hover { background: var(--secondary-hover); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,107,53,0.3); }
        .btn-outline-hero { background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.3); }
        .btn-outline-hero:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

        /* ===== SECTIONS ===== */
        .section { padding: 40px 24px; max-width: 1200px; margin: 0 auto; }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .section-title { font-size: 1.5rem; font-weight: 700; color: var(--dark); }

        .see-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 50px;
            background: var(--primary-light);
        }
        .see-all:hover {
            background: var(--primary);
            color: white;
        }
        .see-all i { transition: var(--transition); }
        .see-all:hover i { transform: translateX(4px); }

        /* ===== CATEGORIES ===== */
        .category-grid { display: flex; flex-wrap: wrap; gap: 12px; }
        .category-card {
            background: var(--white);
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
            color: var(--dark);
        }
        .category-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); border-color: var(--primary-light); }
        .category-card.active { background: var(--primary); color: white; border-color: var(--primary); }
        .category-card i { margin-right: 8px; }

        /* ===== PRODUCT GRID ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }
        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .product-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-hover); }

        .product-image-wrapper { position: relative; padding-top: 100%; background: var(--light-grey); }
        .product-image { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }

        .product-info { padding: 16px; }
        .product-title { font-size: 1rem; font-weight: 600; margin-bottom: 4px; color: var(--dark); }
        .product-location { font-size: 0.8rem; color: var(--grey); margin-bottom: 8px; }
        .product-location i { margin-right: 4px; }
        .product-price { font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-bottom: 12px; }
        .product-actions { display: flex; gap: 8px; }

        .buy-now-btn {
            flex: 1;
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
        }
        .buy-now-btn:hover { background: var(--primary-dark); }

        .verified-badge { display: inline-block; font-size: 0.7rem; color: var(--primary); font-weight: 600; margin-bottom: 4px; }
        .verified-badge i { margin-right: 4px; }

        /* ===== BOTTOM NAV ===== */
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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero h2 { font-size: 1.6rem; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .section { padding: 24px 16px; }
            .hero { padding: 40px 16px; }
            .tagline { display: none; }
        }
        @media (max-width: 480px) {
            .product-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .product-info { padding: 12px; }
            .product-title { font-size: 0.85rem; }
            .product-price { font-size: 1rem; }
            .buy-now-btn { font-size: 0.75rem; padding: 8px; }
            .bottom-nav a { font-size: 0.6rem; min-width: 40px; }
            .bottom-nav a i { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="logo">
            <h1>Safe<span>Hub</span></h1>
            <span class="tagline">Shop local, sell fast</span>
        </div>
        <div class="header-actions">
            <a href="cart.php" aria-label="Cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cartCount">0</span>
            </a>
        </div>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero">
        <h2>Welcome to SafeHub</h2>
        <p>Shop local, sell fast</p>
        <p class="subtitle">South Africa's trusted marketplace for local buyers and sellers</p>
        <div class="hero-buttons">
            <a href="#products" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
            <a href="sell.php" class="btn btn-outline-hero"><i class="fas fa-store"></i> Sell Now</a>
        </div>
    </section>

    <!-- ===== CATEGORIES ===== -->
    <section class="section">
        <div class="section-header">
            <h3 class="section-title">Categories</h3>
            <a class="see-all" id="seeAllCategories"><i class="fas fa-arrow-right"></i> See all</a>
        </div>
        <div class="category-grid">
            <div class="category-card" data-category="clothing"><i class="fas fa-tshirt"></i>Clothing</div>
            <div class="category-card" data-category="food"><i class="fas fa-utensils"></i>Food</div>
            <div class="category-card" data-category="electronics"><i class="fas fa-laptop"></i>Electronics</div>
            <div class="category-card" data-category="furniture"><i class="fas fa-chair"></i>Furniture</div>
            <div class="category-card" data-category="beauty"><i class="fas fa-spa"></i>Beauty</div>
            <div class="category-card" data-category="other"><i class="fas fa-box"></i>Other</div>
        </div>
    </section>

    <!-- ===== PRODUCTS ===== -->
    <section class="section" id="products">
        <div class="section-header">
            <h3 class="section-title">Featured Products</h3>
            <a class="see-all" id="seeAllProducts"><i class="fas fa-arrow-right"></i> See all</a>
        </div>
        <div class="product-grid" id="productGrid">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="product-card" data-category="<?php echo $product['category'] ?? 'other'; ?>">
                        <div class="product-image-wrapper">
                            <img class="product-image" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                        </div>
                        <div class="product-info">
                            <div class="verified-badge"><i class="fas fa-check-circle"></i> Verified seller</div>
                            <div class="product-title"><?php echo $product['name']; ?></div>
                            <div class="product-location"><i class="fas fa-map-marker-alt"></i> <?php echo $product['location'] ?? 'South Africa'; ?></div>
                            <div class="product-price">R<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-actions">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="buy-now-btn"><i class="fas fa-shopping-bag"></i> Buy Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; padding:40px; color:var(--grey); grid-column: 1 / -1;">
                    No products available yet. Be the first to <a href="sell.php" style="color:var(--primary);">sell</a> something!
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== BOTTOM NAV ===== -->
    <div class="bottom-nav">
        <a href="/" class="active"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>Cart</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
        <a href="register.php"><i class="fas fa-user-plus"></i><span>Register</span></a>
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i><span>Admin</span></a>
    </div>

    <script>
        // ============================================
        // CART COUNT
        // ============================================
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('safehubCart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                cartCountElement.textContent = totalItems;
            }
        }

        // ============================================
        // CATEGORY FILTER
        // ============================================
        function filterProducts(category) {
            const productCards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            productCards.forEach(card => {
                const cardCategory = card.dataset.category || 'other';
                if (category === 'all' || cardCategory === category) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const grid = document.querySelector('.product-grid');
            let existingMsg = document.getElementById('noProductsMsg');

            if (visibleCount === 0) {
                if (!existingMsg) {
                    const msg = document.createElement('p');
                    msg.id = 'noProductsMsg';
                    msg.style.cssText = 'text-align:center; padding:40px; color:var(--grey); grid-column: 1 / -1;';
                    msg.textContent = 'No products found in this category.';
                    grid.appendChild(msg);
                }
            } else {
                if (existingMsg) {
                    existingMsg.remove();
                }
            }
        }

        // ============================================
        // EVENT LISTENERS
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();

            // Category filter
            document.querySelectorAll('.category-card').forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.dataset.category || 'all';
                    filterProducts(category);

                    document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // See all for Categories
            document.getElementById('seeAllCategories').addEventListener('click', function(e) {
                e.preventDefault();
                filterProducts('all');
                document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
                this.innerHTML = '<i class="fas fa-check"></i> All shown';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-arrow-right"></i> See all';
                }, 2000);
            });

            // See all for Featured Products
            document.getElementById('seeAllProducts').addEventListener('click', function(e) {
                e.preventDefault();
                filterProducts('all');
                document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
                this.innerHTML = '<i class="fas fa-check"></i> All shown';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-arrow-right"></i> See all';
                }, 2000);
            });

            // Scroll to products
            document.querySelector('.btn-primary').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('#products').scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
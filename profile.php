<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Get time-based greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Get orders
$orders = $conn->query("SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC");

// Get products (if seller)
$products = null;
if ($user_role == 'seller') {
    $products = $conn->query("SELECT * FROM products WHERE seller_id = '$user_id' ORDER BY created_at DESC");
}

// Get total orders count for welcome message
$total_orders = $orders->num_rows;
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - My Profile</title>
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

        .container { max-width: 1000px; margin: 0 auto; padding: 24px; }

        .profile-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--shadow);
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px 28px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .welcome-banner h2 {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .welcome-banner p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-top: 4px;
        }
        .welcome-banner .badge-count {
            background: rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .profile-header h1 { font-size: 1.8rem; color: var(--dark); }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary);
        }

        .user-info { margin-bottom: 24px; }
        .user-info p { margin: 8px 0; font-size: 0.95rem; }
        .user-info p strong { color: var(--dark); }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        .badge-buyer { background: #3B82F6; }
        .badge-seller { background: var(--secondary); }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 24px 0 12px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .section-title i { color: var(--primary); margin-right: 8px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 0.9rem;
        }
        table th, table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table th {
            background: var(--light-grey);
            font-weight: 600;
            color: var(--dark);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #DC2626; color: white; }
        .btn-danger:hover { background: #B91C1C; }
        .btn-edit { background: #F59E0B; color: white; }
        .btn-edit:hover { background: #D97706; }
        .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); }
        .btn-outline:hover { background: var(--primary); color: white; }

        .product-card {
            border: 1px solid #eee;
            border-radius: var(--radius-sm);
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .product-card .name { font-weight: 600; }
        .product-card .price { color: var(--primary); font-weight: 700; }

        .product-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--grey);
        }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

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

        @media (max-width: 600px) {
            .profile-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .tagline { display: none; }
            .welcome-banner { flex-direction: column; text-align: center; gap: 12px; }
            table { font-size: 0.75rem; }
            table th, table td { padding: 6px 8px; }
            .product-card { flex-direction: column; align-items: stretch; }
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
        <a href="/" class="back-link"><i class="fas fa-home"></i> Home</a>
    </header>

    <div class="container">
        <div class="profile-card">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div>
                    <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
                    <p>Welcome back to your SafeHub account. <?php echo $total_orders > 0 ? "You have <strong>{$total_orders}</strong> order(s)." : "Ready to start shopping?"; ?></p>
                </div>
                <div>
                    <span class="badge-count"><i class="fas fa-shopping-bag"></i> <?php echo $total_orders; ?> Orders</span>
                </div>
            </div>

            <div class="profile-header">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div class="profile-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <h1><?php echo htmlspecialchars($user_name); ?></h1>
                        <span style="color:var(--grey); font-size:0.9rem;"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>
                </div>
                <div>
                    <span class="badge <?php echo ($user_role == 'seller') ? 'badge-seller' : 'badge-buyer'; ?>">
                        <i class="fas <?php echo ($user_role == 'seller') ? 'fa-store' : 'fa-shopping-bag'; ?>"></i>
                        <?php echo ucfirst($user_role); ?>
                    </span>
                    <a href="logout.php" class="btn btn-danger" style="margin-left:12px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <div class="user-info">
                <p><strong><i class="fas fa-user"></i> Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
                <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                <p><strong><i class="fas fa-calendar-alt"></i> Member since:</strong> <?php echo date('d M Y'); ?></p>
            </div>

            <?php if ($user_role == 'seller'): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:12px;">
                    <h3 class="section-title" style="margin:0;"><i class="fas fa-box"></i> My Products</h3>
                    <a href="sell.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
                </div>

                <?php if ($products && $products->num_rows > 0): ?>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <div class="product-card">
                            <div>
                                <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="price">R<?php echo number_format($product['price'], 2); ?></div>
                                <div style="font-size:0.8rem; color:var(--grey);">Stock: <?php echo $product['stock']; ?> units</div>
                            </div>
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>You haven't listed any products yet.</p>
                        <a href="sell.php" class="btn btn-primary" style="margin-top:12px;"><i class="fas fa-plus"></i> List Your First Product</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <h3 class="section-title"><i class="fas fa-shopping-bag"></i> My Orders</h3>
            <?php if ($orders && $orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#SA-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>R<?php echo number_format($order['total'], 2); ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <p>You haven't placed any orders yet.</p>
                    <a href="/" class="btn btn-primary" style="margin-top:12px;"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="/"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>Cart</span></a>
        <a href="profile.php" class="active"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
        <a href="register.php"><i class="fas fa-user-plus"></i><span>Register</span></a>
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i><span>Admin</span></a>
    </div>

</body>
</html>
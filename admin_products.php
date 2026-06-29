<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Admin Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #0F7A3E;
            --primary-dark: #0A5C2E;
            --primary-light: #E8F5E9;
            --secondary: #FF6B35;
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
        }

        .admin-header {
            background: var(--white);
            padding: 14px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-header .logo { font-size: 1.3rem; font-weight: 800; color: var(--primary); }
        .admin-header .logo span { color: var(--secondary); }
        .admin-header .admin-info { display: flex; align-items: center; gap: 16px; }

        .admin-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .logout-btn {
            background: #DC2626;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .logout-btn:hover { background: #B91C1C; }

        .admin-container { display: flex; min-height: calc(100vh - 70px); }

        .sidebar {
            width: 240px;
            background: var(--white);
            border-right: 1px solid #eee;
            padding: 20px 0;
            flex-shrink: 0;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--grey);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        .sidebar a i { width: 20px; text-align: center; }
        .sidebar a:hover { background: var(--light-grey); color: var(--dark); }
        .sidebar a.active {
            background: var(--primary-light);
            color: var(--primary);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        .main-content { flex: 1; padding: 24px; }

        .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 24px; }
        .page-title i { color: var(--primary); margin-right: 10px; }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .card-header h3 { font-size: 1.1rem; }
        .card-header h3 i { color: var(--primary); margin-right: 8px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-edit { background: #F59E0B; color: white; }
        .btn-edit:hover { background: #D97706; }
        .btn-danger { background: #DC2626; color: white; }
        .btn-danger:hover { background: #B91C1C; }

        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: var(--light-grey); font-weight: 600; color: var(--dark); }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .empty-state { text-align: center; padding: 40px; color: var(--grey); }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

        .product-image-small {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: var(--radius-sm);
        }

        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #eee; display: flex; flex-wrap: wrap; padding: 10px; }
            .sidebar a { padding: 8px 14px; font-size: 0.8rem; }
            table { font-size: 0.75rem; }
            table th, table td { padding: 6px 8px; }
        }
    </style>
</head>
<body>

    <header class="admin-header">
        <div class="logo">Safe<span>Hub</span> <span style="font-size:0.7rem; color:var(--grey); font-weight:400; margin-left:8px;">Admin</span></div>
        <div class="admin-info">
            <span style="font-size:0.9rem; font-weight:500;">Welcome, Admin</span>
            <div class="admin-avatar"><i class="fas fa-user"></i></div>
            <a href="logout.php"><button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button></a>
        </div>
    </header>

    <div class="admin-container">
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="admin_users.php"><i class="fas fa-users"></i> Users</a>
            <a href="admin_products.php" class="active"><i class="fas fa-box"></i> Products</a>
            <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </div>

        <div class="main-content">
            <h1 class="page-title"><i class="fas fa-box"></i> Products</h1>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> All Products</h3>
                    <span style="font-size:0.8rem; color:var(--grey);">Total: <?php echo $products->num_rows; ?></span>
                </div>

                <?php if ($products && $products->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img class="product-image-small" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>R<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>No products found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
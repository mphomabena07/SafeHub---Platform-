<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

// Get counts
$users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$products_count = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$orders_count = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];

// Get recent orders
$recent_orders = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Admin Dashboard</title>
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

        /* Header */
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

        /* Container */
        .admin-container { display: flex; min-height: calc(100vh - 70px); }

        /* Sidebar */
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

        /* Main Content */
        .main-content { flex: 1; padding: 24px; }

        .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 24px; }
        .page-title i { color: var(--primary); margin-right: 10px; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; color: var(--dark); }
        .stat-card .stat-label { color: var(--grey); font-size: 0.85rem; margin-top: 4px; }
        .stat-card .stat-icon { font-size: 2rem; color: var(--primary-light); float: right; }

        /* Recent Orders Table */
        .table-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }
        .table-card h3 { margin-bottom: 16px; font-size: 1rem; }
        .table-card h3 i { color: var(--primary); margin-right: 8px; }

        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: var(--light-grey); font-weight: 600; color: var(--dark); }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
        }
        .badge-pending { background: #F59E0B; }
        .badge-confirmed { background: #3B82F6; }
        .badge-ready { background: #8B5CF6; }
        .badge-completed { background: #10B981; }
        .badge-cancelled { background: #DC2626; }

        .empty-state { text-align: center; padding: 40px; color: var(--grey); }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #eee; display: flex; flex-wrap: wrap; padding: 10px; }
            .sidebar a { padding: 8px 14px; font-size: 0.8rem; }
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="admin_dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="admin_users.php"><i class="fas fa-users"></i> Users</a>
            <a href="admin_products.php"><i class="fas fa-box"></i> Products</a>
            <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </div>

        <div class="main-content">
            <h1 class="page-title"><i class="fas fa-chart-pie"></i> Dashboard</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo $users_count; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-number"><?php echo $products_count; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-number"><?php echo $orders_count; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-number">0</div>
                    <div class="stat-label">Pending Reviews</div>
                </div>
            </div>

            <div class="table-card">
                <h3><i class="fas fa-clock"></i> Recent Orders</h3>
                <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>User</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td>#SA-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td>User <?php echo $order['user_id']; ?></td>
                                    <td>R<?php echo number_format($order['total'], 2); ?></td>
                                    <td><span class="badge badge-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>No orders yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
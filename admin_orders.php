<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

// Handle update status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $conn->query("UPDATE orders SET status = '$status' WHERE id = '$id'");
    header("Location: admin_orders.php?msg=updated");
    exit();
}

// Handle delete order
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id = '$id'");
    header("Location: admin_orders.php?msg=deleted");
    exit();
}

// Get all orders
$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Admin Orders</title>
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

        .message {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message-success { background: var(--primary-light); color: var(--primary); }
        .message-delete { background: #FEE2E2; color: #DC2626; }

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
        .btn-confirm { background: #3B82F6; color: white; }
        .btn-confirm:hover { background: #2563EB; }
        .btn-ready { background: #8B5CF6; color: white; }
        .btn-ready:hover { background: #7C3AED; }
        .btn-complete { background: #10B981; color: white; }
        .btn-complete:hover { background: #059669; }
        .btn-cancel { background: #DC2626; color: white; }
        .btn-cancel:hover { background: #B91C1C; }
        .btn-delete { background: #DC2626; color: white; }
        .btn-delete:hover { background: #B91C1C; }

        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
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

        .actions { display: flex; gap: 4px; flex-wrap: wrap; }

        .empty-state { text-align: center; padding: 40px; color: var(--grey); }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

        .status-group { display: flex; gap: 4px; flex-wrap: wrap; }

        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #eee; display: flex; flex-wrap: wrap; padding: 10px; }
            .sidebar a { padding: 8px 14px; font-size: 0.8rem; }
            table { font-size: 0.75rem; }
            table th, table td { padding: 6px 8px; }
            .status-group { flex-direction: column; }
            .btn { font-size: 0.7rem; padding: 4px 10px; }
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
            <a href="admin_products.php"><i class="fas fa-box"></i> Products</a>
            <a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
        </div>

        <div class="main-content">
            <h1 class="page-title"><i class="fas fa-shopping-cart"></i> Orders</h1>

            <?php if ($msg == 'updated'): ?>
                <div class="message message-success"><i class="fas fa-check-circle"></i> Order status updated successfully!</div>
            <?php endif; ?>
            <?php if ($msg == 'deleted'): ?>
                <div class="message message-delete"><i class="fas fa-trash"></i> Order deleted successfully!</div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> All Orders</h3>
                    <span style="font-size:0.8rem; color:var(--grey);">Total: <?php echo $orders->num_rows; ?></span>
                </div>

                <?php if ($orders && $orders->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>User ID</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#SA-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo $order['user_id']; ?></td>
                                    <td><strong>R<?php echo number_format($order['total'], 2); ?></strong></td>
                                    <td><span class="badge badge-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <div class="status-group">
                                            <a href="admin_orders.php?id=<?php echo $order['id']; ?>&status=confirmed" class="btn btn-confirm">Confirm</a>
                                            <a href="admin_orders.php?id=<?php echo $order['id']; ?>&status=ready" class="btn btn-ready">Ready</a>
                                            <a href="admin_orders.php?id=<?php echo $order['id']; ?>&status=completed" class="btn btn-complete">Complete</a>
                                            <a href="admin_orders.php?id=<?php echo $order['id']; ?>&status=cancelled" class="btn btn-cancel">Cancel</a>
                                            <a href="admin_orders.php?delete=<?php echo $order['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>No orders found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
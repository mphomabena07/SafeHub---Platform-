<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

// Handle delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = '$id'");
    header("Location: admin_users.php?msg=deleted");
    exit();
}

// Handle add/edit user
$message = '';
$edit_user = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    if ($id) {
        $sql = "UPDATE users SET full_name='$full_name', email='$email', phone='$phone', role='$role', status='$status' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ User updated successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    } else {
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, phone, role, status, password) VALUES ('$full_name', '$email', '$phone', '$role', '$status', '$password')";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ User added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Admin Users</title>
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

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
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
        .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); }
        .btn-outline:hover { background: var(--primary); color: white; }

        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

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
        .badge-buyer { background: #3B82F6; }
        .badge-seller { background: #F59E0B; }
        .badge-admin { background: #8B5CF6; }
        .badge-active { background: #10B981; }
        .badge-verified { background: #3B82F6; }
        .badge-pending { background: #F59E0B; }
        .badge-suspended { background: #DC2626; }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .empty-state { text-align: center; padding: 40px; color: var(--grey); }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #eee; display: flex; flex-wrap: wrap; padding: 10px; }
            .sidebar a { padding: 8px 14px; font-size: 0.8rem; }
            .form-row { grid-template-columns: 1fr; }
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
            <a href="admin_users.php" class="active"><i class="fas fa-users"></i> Users</a>
            <a href="admin_products.php"><i class="fas fa-box"></i> Products</a>
            <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </div>

        <div class="main-content">
            <h1 class="page-title"><i class="fas fa-users"></i> User Management</h1>

            <?php if ($msg == 'deleted'): ?>
                <div class="message message-success"><i class="fas fa-check-circle"></i> User deleted successfully!</div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, '✅') !== false ? 'message-success' : 'message-error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-plus"></i> Add / Edit User</h3>
                    <button class="btn btn-outline" id="cancelBtn" style="display:none;"><i class="fas fa-times"></i> Cancel</button>
                </div>
                <form method="POST" id="userForm">
                    <input type="hidden" name="id" id="userId">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full_name" id="fullName" placeholder="Enter full name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" id="userEmail" placeholder="Enter email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone</label>
                            <input type="text" name="phone" id="userPhone" placeholder="Enter phone number">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> Role</label>
                            <select name="role" id="userRole">
                                <option value="buyer">Buyer</option>
                                <option value="seller">Seller</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-circle"></i> Status</label>
                        <select name="status" id="userStatus">
                            <option value="Active">Active</option>
                            <option value="Verified">Verified</option>
                            <option value="Pending">Pending Verification</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Add User</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> All Users</h3>
                    <span style="font-size:0.8rem; color:var(--grey);">Total: <?php echo $users->num_rows; ?></span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && $users->num_rows > 0): ?>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                    <td><span class="badge badge-<?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-edit" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo addslashes($user['full_name']); ?>', '<?php echo $user['email']; ?>', '<?php echo $user['phone']; ?>', '<?php echo $user['role']; ?>', '<?php echo $user['status']; ?>')"><i class="fas fa-edit"></i> Edit</button>
                                            <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; padding:20px; color:var(--grey);">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function editUser(id, name, email, phone, role, status) {
            document.getElementById('userId').value = id;
            document.getElementById('fullName').value = name;
            document.getElementById('userEmail').value = email;
            document.getElementById('userPhone').value = phone || '';
            document.getElementById('userRole').value = role;
            document.getElementById('userStatus').value = status || 'Active';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update User';
            document.getElementById('cancelBtn').style.display = 'inline-flex';
            document.querySelector('.card-header h3').innerHTML = '<i class="fas fa-edit" style="color:var(--primary);"></i> Edit User';
        }

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Add User';
            document.querySelector('.card-header h3').innerHTML = '<i class="fas fa-user-plus" style="color:var(--primary);"></i> Add / Edit User';
            this.style.display = 'none';
        });
    </script>
</body>
</html>
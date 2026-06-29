<?php
session_start();
include 'db_connect.php';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (full_name, email, phone, password, role) 
                VALUES ('$full_name', '$email', '$phone', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            
            if (isset($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
                exit();
            }
            
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
            } else {
                header("Location: profile.php");
            }
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SafeHub - Register</title>
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
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            padding-bottom: 80px;
        }

        .register-container {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 480px;
        }

        .register-container h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
            text-align: center;
            margin-bottom: 8px;
        }

        .register-container .subtitle {
            text-align: center;
            color: var(--grey);
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: var(--transition);
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 122, 62, 0.3);
        }

        .error {
            background: #FEE2E2;
            color: #DC2626;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            text-align: center;
            font-size: 0.9rem;
        }
        .success {
            background: var(--primary-light);
            color: var(--primary);
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            text-align: center;
            font-size: 0.9rem;
        }

        .link { text-align: center; margin-top: 16px; font-size: 0.9rem; color: var(--grey); }
        .link a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .link a:hover { text-decoration: underline; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: var(--grey);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover { color: var(--primary); }

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

        @media (max-width: 480px) {
            .register-container { padding: 24px; }
            .bottom-nav a { font-size: 0.6rem; min-width: 40px; }
            .bottom-nav a i { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1><i class="fas fa-user-plus" style="color:var(--primary);"></i> Create Account</h1>
        <p class="subtitle">Join SafeHub and start selling or buying today</p>

        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" placeholder="Thabo Mokoena" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="your@email.com" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Phone</label>
                <input type="tel" name="phone" placeholder="071 234 5678">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> I want to...</label>
                <select name="role">
                    <option value="buyer"><i class="fas fa-shopping-bag"></i> Buy Products</option>
                    <option value="seller"><i class="fas fa-store"></i> Sell Products</option>
                </select>
            </div>
            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Register</button>
        </form>

        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        <a href="/" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>

    <div class="bottom-nav">
        <a href="/"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>Cart</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
        <a href="register.php" class="active"><i class="fas fa-user-plus"></i><span>Register</span></a>
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i><span>Admin</span></a>
    </div>

</body>
</html>
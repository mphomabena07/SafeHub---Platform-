<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "safhub_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // 'buyer' or 'seller'
    
    // Check if email already exists
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "Email already registered. Please login.";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (fullname, email, phone, password, role, created_at) 
                VALUES ('$fullname', '$email', '$phone', '$password', '$role', NOW())";
        
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful! Welcome to SafeHub.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
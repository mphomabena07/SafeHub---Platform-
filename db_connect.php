<?php
// Database connection for SafeHub
$host = 'sql105.infinityfree.com'; 
$username = 'if0_42118591'; 
$password = 'Busisiwe00';  
$database = 'if0_42118591_safehub_db'; 

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
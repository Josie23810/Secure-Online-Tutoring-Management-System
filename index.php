<?php
session_start();

// If user is already logged in, redirect based on role
if (isset($_SESSION['role'])) {
    header("Location: dashboards/" . $_SESSION['role'] . ".php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SOTMS | Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            text-align: center;
            padding-top: 100px;
        }
        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background: #2c7be5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background: #1a5fd0;
        }
    </style>
</head>
<body>

    <h1>Secure Online Tutor Management System</h1>
    <p>Welcome to the SOTMS platform</p>

    <a href="auth/login.php">Login</a>
    <a href="auth/register.php">Register</a>

</body>
</html>

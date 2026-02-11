<?php
session_start();
require __DIR__ . "/../includes/db.php";  // Corrected path to DB connection

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, role, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'student':
                    header("Location: ../dashboards/students.php");
                    exit;
                case 'tutor':
                    header("Location: ../dashboards/tutor.php");
                    exit;
                case 'admin':
                    header("Location: ../dashboards/admin.php");
                    exit;
                default:
                    $error = "Role not recognized.";
            }
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SOTMS | Login</title>
    <style>
        /* General body and container */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c7be5, #1a5fd0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            padding: 40px 30px;
            width: 360px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        h1 {
            color: #2c7be5;
            margin-bottom: 5px;
        }

        p {
            color: #555;
            margin-bottom: 25px;
        }

        /* Inputs stacked vertically */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        /* Login button stacked vertically */
        button {
            width: 100%;
            padding: 12px 0;
            margin: 15px 0;
            background-color: #2c7be5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1a5fd0;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .register-link,
        .forgot-link {
            margin-top: 10px;
        }

        .register-link a,
        .forgot-link a {
            color: #2c7be5;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover,
        .forgot-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>SOTMS</h1>
    <p>Secure Online Tutor Management System</p>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="forgot-link">
        <a href="forgot_password.php">Forgot Password?</a>
    </div>

    <div class="register-link">
        Don’t have an account? <a href="register.php">Register here</a>
    </div>
</div>

</body>
</html>
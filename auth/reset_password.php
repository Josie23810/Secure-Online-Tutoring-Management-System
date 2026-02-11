<?php
session_start();
require __DIR__ . "/../includes/db.php";

$error = "";
$message = "";

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];

// Check token validity
$stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    die("Invalid or expired token");
}

if (strtotime($user['reset_expires']) < time()) {
    die("Token has expired");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "Passwords do not match";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $update->bind_param("si", $password_hash, $user['id']);
        if ($update->execute()) {
            $message = "Password successfully reset! <a href='login.php'>Login here</a>";
        } else {
            $error = "Failed to reset password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | SOTMS</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #2c7be5, #1a5fd0); display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .container { background:#fff; padding:40px 30px; width:360px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.2); text-align:center; }
        h1 { color:#2c7be5; margin-bottom:5px; }
        p { color:#555; margin-bottom:25px; }
        input[type="password"] { width:100%; padding:12px 15px; margin:10px 0; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        button { width:100%; padding:12px 0; margin:15px 0; background-color:#2c7be5; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
        button:hover { background-color:#1a5fd0; }
        .message { color:green; font-weight:bold; margin-bottom:10px; }
        .error { color:red; margin-bottom:10px; font-weight:bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Reset Password</h1>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New password" required>
            <input type="password" name="password_confirm" placeholder="Confirm password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

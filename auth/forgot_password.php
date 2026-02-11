<?php
session_start();
require __DIR__ . "/../includes/db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Generate a secure token
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token and expiration in database
        $update = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $update->bind_param("ssi", $token, $expires, $user['id']);
        $update->execute();

        // Send email
        $reset_link = "http://localhost/sotms/auth/reset_password.php?token=" . $token;
        $subject = "SOTMS Password Reset";
        $body = "Hi,\n\nClick the link below to reset your password:\n\n" . $reset_link . "\n\nThis link will expire in 1 hour.";
        $headers = "From: no-reply@sotms.local";

        if (mail($email, $subject, $body, $headers)) {
            $message = "A password reset link has been sent to your email.";
        } else {
            $error = "Unable to send email. Make sure your PHP mail() is configured.";
        }

    } else {
        $message = "If this email is registered, a password reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | SOTMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c7be5, #1a5fd0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 40px 30px;
            width: 360px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 { color: #2c7be5; margin-bottom: 5px; }
        p { color: #555; margin-bottom: 25px; }
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
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
        }
        button:hover { background-color: #1a5fd0; }
        .message { color: green; font-weight: bold; margin-bottom: 10px; }
        .error { color: red; margin-bottom: 10px; font-weight: bold; }
        a { color: #2c7be5; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h1>Forgot Password</h1>
    <p>Enter your email to reset your password</p>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <p><a href="login.php">Back to Login</a></p>
</div>

</body>
</html>

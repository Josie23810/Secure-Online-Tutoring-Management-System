<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$user_id = $_GET['id'] ?? null;
$message = "";
$user = $profile = $sessions = [];

if ($user_id) {
    // Fetch user
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Fetch profile
    if ($user['role'] == 'student') {
        $stmt = $conn->prepare("SELECT * FROM student_profiles WHERE student_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
    } elseif ($user['role'] == 'tutor') {
        $stmt = $conn->prepare("SELECT * FROM tutor_profiles WHERE tutor_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
    }

    // Fetch sessions
    $stmt = $conn->prepare("SELECT * FROM sessions WHERE student_id = ? OR tutor_id = ?");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update user
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // Update profile
    if ($user['role'] == 'student') {
        $interests = $_POST['interests'];
        $goals = $_POST['goals'];
        $stmt = $conn->prepare("UPDATE student_profiles SET interests=?, goals=? WHERE student_id=?");
        $stmt->bind_param("ssi", $interests, $goals, $user_id);
        $stmt->execute();
    } elseif ($user['role'] == 'tutor') {
        $qualifications = $_POST['qualifications'];
        $hourly_rate = $_POST['hourly_rate'];
        $stmt = $conn->prepare("UPDATE tutor_profiles SET qualifications=?, hourly_rate=? WHERE tutor_id=?");
        $stmt->bind_param("sdi", $qualifications, $hourly_rate, $user_id);
        $stmt->execute();
    }

    $message = "User updated.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - SOTMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c7be5, #1a5fd0);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 40px 30px;
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #2c7be5;
            margin-bottom: 20px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px 0;
            margin: 15px 0;
            background-color: #2c7be5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .message {
            color: green;
            font-weight: bold;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($user): ?>
            <form method="POST">
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <?php if ($user['role'] == 'student'): ?>
                    <textarea name="interests" placeholder="Interests"><?php echo htmlspecialchars($profile['interests'] ?? ''); ?></textarea>
                    <textarea name="goals" placeholder="Goals"><?php echo htmlspecialchars($profile['goals'] ?? ''); ?></textarea>
                <?php elseif ($user['role'] == 'tutor'): ?>
                    <textarea name="qualifications" placeholder="Qualifications"><?php echo htmlspecialchars($profile['qualifications'] ?? ''); ?></textarea>
                    <input type="number" step="0.01" name="hourly_rate" placeholder="Hourly Rate" value="<?php echo htmlspecialchars($profile['hourly_rate'] ?? ''); ?>">
                <?php endif; ?>
                <button type="submit">Update</button>
            </form>
            <h2>Sessions</h2>
            <ul>
                <?php foreach ($sessions as $session): ?>
                    <li><?php echo htmlspecialchars($session['subject'] . ' - ' . $session['status']); ?> <a href="?delete_session=<?php echo $session['id']; ?>" onclick="return confirm('Delete?')">Delete</a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="admin.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_name = $user['name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SOTMS</title>
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
        .dashboard-container {
            background: #fff;
            padding: 40px 30px;
            width: 400px;
            max-width: 90%;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #2c7be5;
            margin-bottom: 10px;
        }
        p {
            color: #555;
            margin-bottom: 25px;
        }
        .welcome {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .action-btn {
            display: block;
            width: 100%;
            padding: 12px 0;
            margin: 10px 0;
            background-color: #2c7be5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
            box-sizing: border-box;
        }
        .action-btn:hover {
            background-color: #1a5fd0;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>SOTMS</h1>
        <p>Secure Online Tutor Management System</p>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($user_name); ?>!</div>
        
        <a href="../sessions/book_sessions.php" class="action-btn">Book Tutor Session</a>
        <a href="../sessions/my_sessions.php" class="action-btn">My Sessions</a>
        <a href="view_tutors.php" class="action-btn">View Tutors</a>  <!-- New: View tutors -->
        <a href="edit_student_profile.php" class="action-btn">My Profile</a>  <!-- New: Edit student profile -->
        <a href="../auth/logout.php" class="action-btn logout-btn">Logout</a>
    </div>
</body>
</html>
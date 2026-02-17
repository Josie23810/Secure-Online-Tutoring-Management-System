<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$student_id = $_SESSION['user_id'];
$message = "";

// Fetch existing profile
$profile = [];
$stmt = $conn->prepare("SELECT * FROM student_profiles WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $interests = trim($_POST['interests']);
    $goals = trim($_POST['goals']);

    if ($profile) {
        $stmt = $conn->prepare("UPDATE student_profiles SET name=?, interests=?, goals=? WHERE student_id=?");
        $stmt->bind_param("sssi", $name, $interests, $goals, $student_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO student_profiles (student_id, name, interests, goals) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $student_id, $name, $interests, $goals);
    }
    if ($stmt->execute()) {
        $message = "Profile updated!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - SOTMS</title>
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
            max-width: 500px;
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
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
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
            transition: 0.3s;
        }
        button:hover {
            background-color: #1a5fd0;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
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
        <h1>Edit Student Profile</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
            <textarea name="interests" placeholder="Interests (e.g., subjects you like)" rows="3"><?php echo htmlspecialchars($profile['interests'] ?? ''); ?></textarea>
            <textarea name="goals" placeholder="Goals (e.g., improve in math)" rows="3"><?php echo htmlspecialchars($profile['goals'] ?? ''); ?></textarea>
            <button type="submit">Save Profile</button>
        </form>
        <a href="students.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
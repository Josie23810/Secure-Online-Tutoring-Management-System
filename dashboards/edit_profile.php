<?php
require "../includes/auth_check.php";  // Ensures only tutors can access
require "../includes/db.php";

$tutor_id = $_SESSION['user_id'];
$message = "";

// Fetch existing profile
$profile = [];
$stmt = $conn->prepare("SELECT * FROM tutor_profiles WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $qualifications = trim($_POST['qualifications']);
    $study_materials = trim($_POST['study_materials']);
    $bio = trim($_POST['bio']);
    $hourly_rate = $_POST['hourly_rate'];

    if ($profile) {
        // Update existing
        $stmt = $conn->prepare("UPDATE tutor_profiles SET name=?, qualifications=?, study_materials=?, bio=?, hourly_rate=? WHERE tutor_id=?");
        $stmt->bind_param("ssssdi", $name, $qualifications, $study_materials, $bio, $hourly_rate, $tutor_id);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO tutor_profiles (tutor_id, name, qualifications, study_materials, bio, hourly_rate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $tutor_id, $name, $qualifications, $study_materials, $bio, $hourly_rate);
    }
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        // Refresh profile data
        $stmt = $conn->prepare("SELECT * FROM tutor_profiles WHERE tutor_id = ?");
        $stmt->bind_param("i", $tutor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
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
    <title>Edit Profile - SOTMS</title>
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
            transition: 0.3s;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
            <textarea name="qualifications" placeholder="Qualifications (e.g., degrees, certifications)" rows="3"><?php echo htmlspecialchars($profile['qualifications'] ?? ''); ?></textarea>
            <textarea name="study_materials" placeholder="Study Materials (e.g., books, resources)" rows="3"><?php echo htmlspecialchars($profile['study_materials'] ?? ''); ?></textarea>
            <textarea name="bio" placeholder="Bio (short description)" rows="3"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
            <input type="number" step="0.01" name="hourly_rate" placeholder="Hourly Rate (e.g., 50.00)" value="<?php echo htmlspecialchars($profile['hourly_rate'] ?? ''); ?>">
            <button type="submit">Save Profile</button>
        </form>
        <a href="tutor.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
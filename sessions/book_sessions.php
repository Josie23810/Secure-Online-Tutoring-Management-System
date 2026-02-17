<?php
require "../includes/auth_check.php";  // Ensures only authenticated students can access
require "../includes/db.php";  // DB connection

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";
$selected_tutor = $_GET['tutor_id'] ?? null;

// Fetch available tutors
$tutors = [];
$stmt = $conn->prepare("SELECT u.id, tp.name FROM users u LEFT JOIN tutor_profiles tp ON u.id = tp.tutor_id WHERE u.role = 'tutor'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $tutors[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tutor_id = $_POST['tutor_id'];
    $start_time = $_POST['start_time'];
    $duration = trim($_POST['duration']);
    $subject = trim($_POST['subject']);
    $status = 'booked';
    $video_link = trim($_POST['video_link']);

    if (empty($tutor_id) || empty($start_time)) {
        $error = "Please select a tutor and enter a start time.";
    } else {
        $stmt = $conn->prepare("INSERT INTO sessions (student_id, tutor_id, start_time, duration, subject, status, video_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $user_id, $tutor_id, $start_time, $duration, $subject, $status, $video_link);
        if ($stmt->execute()) {
            $success = "Session booked successfully!";
        } else {
            $error = "Error booking session.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tutor Session - SOTMS</title>
    <style>
        /* General body and container - Matching login/dashboard style */
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

        .book-container {
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
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 25px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .success {
            color: green;
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* Form elements */
        select,
        input[type="datetime-local"],
        input[type="text"],
        textarea {
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
    <div class="book-container">
        <h1>Book Tutor Session</h1>
        <p>Select a tutor and schedule your session</p>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <select name="tutor_id" required>
                <option value="">Select a Tutor</option>
                <?php foreach ($tutors as $tutor): ?>
                    <option value="<?php echo $tutor['id']; ?>" <?php if ($selected_tutor == $tutor['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($tutor['name'] ?? 'Tutor ' . $tutor['id']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="start_time" required>
            <input type="text" name="duration" placeholder="Duration (e.g., 60 minutes)">
            <input type="text" name="subject" placeholder="Subject (e.g., Mathematics)">
            <input type="text" name="video_link" placeholder="Video Link (optional)">
            <button type="submit">Book Session</button>
        </form>

        <a href="../dashboards/students.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
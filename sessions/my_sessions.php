<?php
require "../includes/auth_check.php";  // Ensures only authenticated students can access
require "../includes/db.php";  // DB connection

$user_id = $_SESSION['user_id'];
$error = "";
$sessions = [];

// Fetch sessions for the logged-in student (updated to match your table columns)
$query = "SELECT s.id, s.start_time, s.status, s.subject, s.duration, s.video_link, u.name AS tutor_name FROM sessions s JOIN users u ON s.tutor_id = u.id WHERE s.student_id = ? ORDER BY s.start_time DESC";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    $error = "Database error: " . $conn->error . ". Check if 'start_time' and other columns exist in 'sessions' table.";
} else {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sessions[] = $row;
            }
        } else {
            $error = "No sessions found.";
        }
    } else {
        $error = "Database error: Failed to execute query - " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - SOTMS</title>
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

        .sessions-container {
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
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 25px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .session-list {
            list-style: none;
            padding: 0;
        }

        .session-item {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 15px;
            border-radius: 6px;
            border-left: 5px solid #2c7be5;
            text-align: left;
        }

        .session-item strong {
            color: #2c7be5;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2c7be5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background-color: #1a5fd0;
        }
    </style>
</head>
<body>
    <div class="sessions-container">
        <h1>My Sessions</h1>
        <p>View your booked tutor sessions</p>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <ul class="session-list">
                <?php foreach ($sessions as $session): ?>
                    <li class="session-item">
                        <strong>Tutor:</strong> <?php echo htmlspecialchars($session['tutor_name']); ?><br>
                        <strong>Start Time:</strong> <?php echo htmlspecialchars($session['start_time']); ?><br>
                        <strong>Status:</strong> <?php echo htmlspecialchars($session['status']); ?><br>
                        <strong>Subject:</strong> <?php echo htmlspecialchars($session['subject'] ?? 'None'); ?><br>
                        <strong>Duration:</strong> <?php echo htmlspecialchars($session['duration'] ?? 'N/A'); ?><br>
                        <strong>Video Link:</strong> <?php echo htmlspecialchars($session['video_link'] ?? 'None'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="../dashboards/students.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
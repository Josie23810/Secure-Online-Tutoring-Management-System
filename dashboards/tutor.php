<?php
require "../includes/auth_check.php";  // Ensures only authenticated tutors can access
require "../includes/db.php";

$tutor_id = $_SESSION['user_id'];
$error = "";

// Fetch profile
$profile = null;
$stmt = $conn->prepare("SELECT * FROM tutor_profiles WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}

// Fetch availability
$availability = [];
$stmt = $conn->prepare("SELECT * FROM tutor_availability WHERE tutor_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $availability[] = $row;
}

// Fetch classes offered
$classes = [];
$stmt = $conn->prepare("SELECT * FROM tutor_classes WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

// Fetch booked sessions
$sessions = [];
$query = "SELECT s.id, s.start_time, s.status, s.subject, u.name AS student_name FROM sessions s JOIN users u ON s.student_id = u.id WHERE s.tutor_id = ? ORDER BY s.start_time DESC";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
} else {
    $error = "Database error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard - SOTMS</title>
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

        .dashboard-container {
            background: #fff;
            padding: 40px 30px;
            width: 100%;
            max-width: 800px;
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

        .section {
            margin: 20px 0;
            text-align: left;
        }

        .section h2 {
            color: #2c7be5;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .item {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            border-left: 5px solid #2c7be5;
        }

        .action-btn {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 15px;
            background-color: #2c7be5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
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

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>SOTMS</h1>
        <p>Tutor Dashboard</p>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>My Profile</h2>
            <?php if ($profile): ?>
                <div class="item">
                    <strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?><br>
                    <strong>Qualifications:</strong> <?php echo htmlspecialchars($profile['qualifications']); ?><br>
                    <strong>Study Materials:</strong> <?php echo htmlspecialchars($profile['study_materials']); ?><br>
                    <strong>Bio:</strong> <?php echo htmlspecialchars($profile['bio']); ?><br>
                    <strong>Hourly Rate:</strong> $<?php echo htmlspecialchars($profile['hourly_rate']); ?>
                </div>
            <?php else: ?>
                <p>No profile set up yet.</p>
            <?php endif; ?>
            <a href="edit_profile.php" class="action-btn">Edit Profile</a>
        </div>

        <div class="section">
            <h2>My Availability (Timelines)</h2>
            <?php if ($availability): ?>
                <?php foreach ($availability as $slot): ?>
                    <div class="item">
                        <strong><?php echo htmlspecialchars($slot['day_of_week']); ?>:</strong> <?php echo htmlspecialchars($slot['start_time']); ?> - <?php echo htmlspecialchars($slot['end_time']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No availability set.</p>
            <?php endif; ?>
            <a href="edit_availability.php" class="action-btn">Manage Availability</a>
        </div>

        <div class="section">
            <h2>Classes Offered</h2>
            <?php if ($classes): ?>
                <?php foreach ($classes as $class): ?>
                    <div class="item">
                        <strong><?php echo htmlspecialchars($class['subject']); ?>:</strong> <?php echo htmlspecialchars($class['description']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No classes listed.</p>
            <?php endif; ?>
            <a href="edit_classes.php" class="action-btn">Manage Classes</a>
        </div>

        <div class="section">
            <h2>Booked Sessions</h2>
            <?php if ($sessions): ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="item">
                        <strong>Student:</strong> <?php echo htmlspecialchars($session['student_name']); ?><br>
                        <strong>Start Time:</strong> <?php echo htmlspecialchars($session['start_time']); ?><br>
                        <strong>Subject:</strong> <?php echo htmlspecialchars($session['subject']); ?><br>
                        <strong>Status:</strong> <?php echo htmlspecialchars($session['status']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No sessions booked.</p>
            <?php endif; ?>
        </div>

        <a href="../auth/logout.php" class="action-btn logout-btn">Logout</a>
    </div>
</body>
</html>